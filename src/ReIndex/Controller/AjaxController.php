<?php

/**
 * @file AjaxController.php
 * @brief This file contains the AjaxController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Controller;


use EoC\Couch;
use Phalcon\Mvc\View;

use ReIndex\Security\Role\AdminRole\ChangeVisibilityPermission;
use ReIndex\Security\Role\ModeratorRole\ProtectPostPermission;
use ReIndex\Security\Role\ModeratorRole\UnprotectPostPermission;
use ReIndex\Security\Role\ModeratorRole\MoveRevisionToTrashPermission;
use ReIndex\Security\Role\ModeratorRole\RestoreRevisionPermission;


/**
 * @brief Controller for the AJAX requests.
 * @nosubgrouping
 */
final class AjaxController extends BaseController {


  /**
   * @brief Gets the moderator menu.
   */
  public function moderatorMenuAction() {
    try {
      if ($this->request->hasPost('id')) {
        $post = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $this->request->getPost('id'));

        $this->view->setVar('post', $post);

        $this->view->setVar('canProtect', $this->user->has(new ProtectPostPermission($post)));
        $this->view->setVar('canUnprotect', $this->user->has(new UnprotectPostPermission($post)));
        $this->view->setVar('canMoveToTrash', $this->user->has(new MoveRevisionToTrashPermission($post)));
        $this->view->setVar('canRestore', $this->user->has(new RestoreRevisionPermission($post)));

        $this->view->pick('views/ajax/moderator-menu');
      }
      else
        throw new \RuntimeException("La risorsa non è più disponibile.");
    }
    catch (\Exception $e) {
      echo json_encode([FALSE, $e->getMessage()]);
    }
  }

}
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


/**
 * @brief Controller for the AJAX requests.
 * @nosubgrouping
 */
class AjaxController extends BaseController {


  /**
   * @brief Gets the moderator menu.
   */
  public function moderatorMenuAction() {
    try {
      if ($this->request->hasPost('id')) {
        $post = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $this->request->getPost('id'));

        $this->view->setVar('post', $post);
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
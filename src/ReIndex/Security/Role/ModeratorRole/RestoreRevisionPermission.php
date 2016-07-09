<?php

/**
 * @file RestoreRevisionPermission.php
 * @brief This file contains the RestoreRevisionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ModeratorRole;


use ReIndex\Security\Role\AbstractPermission;
use ReIndex\Doc\Versionable;
use ReIndex\Enum\State;

use EoC\Couch;


/**
 * @brief Permission to restore a deleted content.
 */
class RestoreRevisionPermission extends AbstractPermission {


  /**
   * @brief Constructor.
   * @param[in] Doc::Versionable $context
   */
  public function __construct(Versionable $context = NULL) {
    parent::__construct($context);
  }


  /**
   * @brief Permission to restore a deleted content
   */
  public function getDescription() {
    return "Permission to restore a deleted content.";
  }


  /**
   * @brief A moderator (or a member with a superior role) can restore a content, but only if the content has been
   * deleted by a member with an inferior role or by himself.
   */
  public function check() {
    if (!$this->context->state->is(State::DELETED))
      return FALSE;
    elseif ($this->context->dustmanId == $this->user->id)
      return TRUE;
    else {
      $dustman = $this->di['couchdb']->getDoc(Couch::STD_DOC_PATH, $this->context->dustmanId);
      return !$dustman->roles->areSuperiorThan($this->getRole()) ? TRUE : FALSE;
    }
  }


}
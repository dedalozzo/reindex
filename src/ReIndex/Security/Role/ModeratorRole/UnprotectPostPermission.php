<?php

/**
 * @file ModeratorRole/UnprotectPostPermission.php
 * @brief This file contains the UnprotectPostPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\ModeratorRole;


use ReIndex\Security\Role\AbstractPermission;
use EoC\Couch;


/**
 * @brief Permission to unprotect a post.
 * @details A moderator (or a member with a superior role) can unprotect only the current revision of a post, just in
 * case it has an active protection. A moderator can unprotect only a post protected by himself, but an admin is able to
 * unprotect a post protected by a moderator; so a superuser is able to unprotect a post protected by an admin.
 */
class UnprotectPostPermission extends AbstractPermission {


  /**
   * @brief Constructor.
   * @param[in] Model::Post $context
   */
  public function __construct($context = NULL) {
    parent::__construct($context);
  }


  public function getDescription() {
    return "Permission to unprotect a content.";
  }


  public function check() {
    if (!$this->context->isProtected())
      return FALSE;
    elseif ($this->context->protectorId === $this->user->id)
      return TRUE;
    else {
      $protector = $this->di['couchdb']->getDoc(Couch::STD_DOC_PATH, $this->context->bannerId);
      return !$protector->roles->isSuperior($this->getRole()) ? TRUE : FALSE;
    }
  }

}
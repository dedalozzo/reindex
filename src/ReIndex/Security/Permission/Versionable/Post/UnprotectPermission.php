<?php

/**
 * @file Post/UnprotectPermission.php
 * @brief This file contains the UnprotectPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post;


use ReIndex\Doc\Post;

use EoC\Couch;


/**
 * @brief Permission to unprotect an post.
 * @details A moderator (or a member with a superior role) can unprotect only the current revision of a post, just in
 * case it has an active protection. A moderator can unprotect only a post protected by himself, but an admin is able to
 * unprotect a post protected by a moderator; so a superuser is able to unprotect a post protected by an admin.
 */
class UnprotectPermission extends ProtectPermission  {

  /**
   * @var Couch $couch
   */
  protected $couch;


  /**
   * @brief Constructor.
   * @param[in] Doc::Post $post
   */
  public function __construct(Post $post) {
    parent::__construct($post);
    $this->couch = $this->di['couchdb'];
  }


  public function getDescription() {
    return "Permission to unprotect a content.";
  }


  /**
   * @brief Returns `true` if the post can be unprotected, `false` otherwise.
   * @retval bool
   */
  public function checkForModeratorRole() {
    if (!$this->post->isProtected())
      return FALSE;
    elseif ($this->post->protectorId === $this->user->id)
      return TRUE;
    else {
      $protector = $this->couch->getDoc('members', Couch::STD_DOC_PATH, $this->post->bannerId);
      return !$protector->roles->areSuperiorThan($this->getRole()) ? TRUE : FALSE;
    }
  }

}
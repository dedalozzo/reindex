<?php

/**
 * @file EditPostPermission.php
 * @brief This file contains the EditPostPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\MemberRole;


use ReIndex\Security\Role\AbstractPermission;


/**
 * @brief Permission to edit a post.
 */
class EditPostPermission extends AbstractPermission {
  protected $post;


  public function __construct($post) {
    parent::__construct();
    $this->post = $post;
  }


  public function getDescription() {
    return "Permission to edit a post.";
  }


  /**
   * @brief Returns `true` if the user is the creator of the post and the post is unlocked, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if ($this->user->match($this->post->creatorId) &&
      !$this->post->isLocked() &&
      ($this->post->state->isCurrent() or $this->post->state->isDraft()))
      return TRUE;
    else
      return FALSE;
  }

}
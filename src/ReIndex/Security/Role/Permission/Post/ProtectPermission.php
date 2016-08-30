<?php

/**
 * @file Post/ProtectPermission.php
 * @brief This file contains the ProtectPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Posts related permissions
namespace ReIndex\Security\Role\Permission\Post;


use ReIndex\Security\Role\Permission\AbstractPermission;
use ReIndex\Doc\Post;
use ReIndex\Enum\State;


/**
 * @brief Permission to close or lock a post.
 * @details A moderator can protect only the current revision of a post, just in case it doesn't have any active
 * protection.
 * @nosubgrouping
 */
class ProtectPermission extends AbstractPermission {

  protected $post;

  /**
   * @brief Constructor.
   * @param[in] Doc::Post $context
   */
  public function __construct(Post $post) {
    parent::__construct();
    $this->post = $post;
  }


  public function getDescription() {
    return "Permission to close or lock a post.";
  }


  /**
   * @brief Returns `true` if the post can be protected, `false` otherwise.
   * @retval bool
   */
  public function checkForModeratorRole() {
    return (!$this->post->isProtected() && $this->post->state->is(State::CURRENT)) ? TRUE : FALSE;
  }

}
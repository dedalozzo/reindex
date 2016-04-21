<?php

/**
 * @file ViewPostPermission.php
 * @brief This file contains the ViewPostPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Permissions for the guest role.
namespace ReIndex\Security\Role\GuestRole;


use ReIndex\Security\Role\AbstractPermission;


/**
 * @brief Permission to read a post.
 * @retval bool
 */
class ViewPostPermission extends AbstractPermission {
  public $post;


  /**
   * @brief Constructor.
   * @param[in] Post $post A post.
   */
  public function __construct($post = NULL) {
    parent::__construct();
    $this->post = $post;
  }
  
  
  public function getDescription() {
    return "Permission to read a post.";
  }


  public function check() {
    return $this->post->state->isCurrent() ? TRUE : FALSE;
  }

}
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
use ReIndex\Model\Post;


/**
 * @brief Permission to read a post.
 */
class ViewPostPermission extends AbstractPermission {


  /**
   * @brief Constructor.
   * @param[in] Model::Post $context.
   */
  public function __construct($context) {
    parent::__construct($context);
  }
  
  
  public function getDescription() {
    return "Permission to read a post.";
  }


  public function check() {
    return $this->context->state->isCurrent() ? TRUE : FALSE;
  }

}
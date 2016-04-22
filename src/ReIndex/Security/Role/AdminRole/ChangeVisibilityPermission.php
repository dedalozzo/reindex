<?php

/**
 * @file ChangeVisibilityPermission.php
 * @brief This file contains the ChangeVisibilityPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\AdminRole;


use ReIndex\Security\Role\AbstractPermission;
use ReIndex\Model\Post;


/**
 * @brief Permission to change the visibility of a content.
 */
class ChangeVisibilityPermission extends AbstractPermission {


  public function __construct(Post $context = NULL) {
    parent::__construct($context);
  }


  public function getDescription() {
    return "Permission to change the visibility of a content.";
  }


  public function check() {
    return $this->context->state->isCurrent() ? TRUE : FALSE;
  }

}
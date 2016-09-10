<?php

/**
 * @file Post/AbstractPermission.php
 * @brief This file contains the AbstractPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Posts related permissions
namespace ReIndex\Security\Permission\Revision\Post;


use ReIndex\Security\Permission\AbstractPermission as Superclass;
use ReIndex\Doc\Post;


abstract class AbstractPermission extends Superclass {

  protected $post;


  /**
   * @brief Constructor.
   * @param[in] Doc::Post $post
   */
  public function __construct(Post $post) {
    $this->post = $post;
    parent::__construct();
  }

}
<?php
/**
 * @file AbstractPostPermission.php
 * @brief This file contains the ${CLASS_NAME} class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission;


use ReIndex\Model\Post;


abstract class AbstractPostPermission extends AbstractPermission {

  protected $post;


  public function __construct(Post $post) {
    parent::__construct();
    $this->post = $post;
  }

}
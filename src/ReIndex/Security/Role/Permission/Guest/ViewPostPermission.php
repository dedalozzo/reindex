<?php
/**
 * @file ViewPostPermission.php
 * @brief This file contains the ${CLASS_NAME} class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Guest;


use ReIndex\Security\Role\Permission\AbstractPermission;
use ReIndex\Model\Post;


class ViewPostPermission extends AbstractPermission {

  public $post;


  public function __construct(Post $post = NULL) {
    parent::__construct();
    $this->post = $post;
  }
  
  
  public function getDescription() {
    //! @todo: Implement getDescription() method.
  }


  /**
   * @brief Returns `true` if the post can be viewed by a guest, `false` otherwise.
   * @retval bool
   */
  public function check() {
    return ($this->post->state->isCurrent()) ? TRUE : FALSE;
  }

}
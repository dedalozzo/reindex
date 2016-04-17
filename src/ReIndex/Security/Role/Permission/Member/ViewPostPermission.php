<?php
/**
 * @file ViewPostPermission.php
 * @brief This file contains the ${CLASS_NAME} class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Member;


use ReIndex\Security\Role\Permission\Guest\ViewPostPermission as Superclass;


class ViewPostPermission extends Superclass {

  public function check() {
    return $this->user->match($this->post->creatorId) ? TRUE : FALSE;
  }

}
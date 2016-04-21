<?php

/**
 * @file ViewPostPermission.php
 * @brief This file contains the ViewPostPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Member;


use ReIndex\Security\Role\Permission\Guest\ViewPostPermission as Superclass;


class ViewPostPermission extends Superclass {

  public function check() {
    if (parent::check())
      return TRUE;
    else
      return $this->user->match($this->post->creatorId) ? TRUE : FALSE;
  }

}
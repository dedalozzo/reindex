<?php

/**
 * @file ProtectPostPermission.php
 * @brief This file contains the ProtectPostPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\AdminRole;


use ReIndex\Security\Role\ModeratorRole\ProtectPostPermission as Superclass;


/**
 * @copydoc ModeratorRole::ProtectPostPermission
 */
class ProtectPostPermission extends Superclass {


  public function check() {
    if (parent::check())
      return TRUE;
    else
      return ($this->context->state->isCurrent() or $this->context->state->isDraft()) ? TRUE : FALSE;
  }

}
<?php
/**
 * @file DebugPermission.php
 * @brief This file contains the DebugPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Developer;


use ReIndex\Security\Role\Permission\AbstractPermission;


class DebugPermission extends AbstractPermission {


  /**
   * @brief Returns `true` if the user can enable the debugger, `false` otherwise.
   * @retval bool
   */
  public function check() {
    return TRUE;
  }

}
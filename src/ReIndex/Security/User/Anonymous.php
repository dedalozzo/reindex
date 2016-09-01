<?php

/**
 * @file Anonymous.php
 * @brief This file contains the Anonymous class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\User;


use ReIndex\Security\Role\GuestRole;
use ReIndex\Security\Permission\IPermission;
use ReIndex\Helper\ClassHelper;


/**
 * @brief This class represents an anonymous user.
 * @nosubgrouping
 */
final class Anonymous implements IUser {


  /**
   * @brief This implementation returns always `null`.
   * @retval null
   */
  public function getId() {
    return NULL;
  }


  /**
   * @brief This implementation returns always `false`.
   * @param[in] string $id The id to match.
   * @retval bool
   */
  public function match($id) {
    return FALSE;
  }


  /**
   * @copydoc IUser::has()
   */
  public function has(IPermission $permission) {
    $role = new GuestRole();

    $permissionReflection = new \ReflectionObject($permission);

    if ($permissionReflection->hasMethod('checkForGuestRole')) { // If a method exists for the roleName...
      // Gets the method.
      $method = $permissionReflection->getMethod('checkForGuestRole');

      $permission->setRole($role);

      // Invokes the method.
      return $method->invoke($this);
    }
    else
      return FALSE;
  }


  /**
   * @brief This implementation returns always `true`.
   * @retval bool
   */
  public function isGuest() {
    return TRUE;
  }


  /**
   * @brief This implementation returns always `false`.
   * @retval bool
   */
  public function isMember() {
    return FALSE;
  }

}
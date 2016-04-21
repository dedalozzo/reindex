<?php

/**
 * @file Anonymous.php
 * @brief This file contains the Anonymous class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\User;


use ReIndex\Security\Role\GuestRole;
use ReIndex\Security\Role\IPermission;
use ReIndex\Helper\ClassHelper;


/**
 * @brief This class represents an anonymous user.
 * @nosubgrouping
 */
class Anonymous implements IUser {


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
    $result = FALSE;

    $role = new GuestRole();

    // Creates the reflection classes.
    $roleReflection = new \ReflectionObject($role);
    $permissionReflection = new \ReflectionObject($permission);

    // Determines the namespace excluded the role name.
    $root = ClassHelper::getClassRoot($permissionReflection->getNamespaceName());

    // Determines the permission class related to the roleName.
    $newPermissionClass = $root . $roleReflection->getShortName() . '\\' . $permissionReflection->getShortName();

    if (class_exists($newPermissionClass)) { // If a permission exists for the role...
      // Casts the original permission object to an instance of the determined class.
      $obj = $permission->castAs($newPermissionClass);

      // Invokes on it the check() method.
      $result = $obj->check();
    }

    return $result;
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
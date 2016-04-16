<?php

/**
 * @file Anonymous.php
 * @brief This file contains the Anonymous class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\User;


use ReIndex\Security\Role\GuestRole;
use ReIndex\Security\Role\Permission\IPermission;
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

    // Gets the class name of the provided instance, pruned by its namespace.
    $className = ClassHelper::getClassName(get_class($permission));

    // Creates a reflection class for the role.
    $reflection = new \ReflectionClass($role);

    // Gets the namespace for the role pruned of the class name.
    $namespaceName = $reflection->getNamespaceName();

    // Determines the permission class related to the role.
    $class = $namespaceName . '\\Permission\\' . $role->getName() . '\\' . $className;

    if (class_exists($class)) { // If a permission exists for the role...
      // Casts the original permission object to an instance of the determined class.
      $obj = $permission->castAs($class);

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
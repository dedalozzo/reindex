<?php
/**
 * @file AbstractRole.php
 * @brief This file contains the AbstractRole class.
 * @details
 * @author Filippo F. Fadda
 */


//! Classes to describe roles
namespace ReIndex\Security\Role;


use ReIndex\Security\Role\Permission\IPermission;


/**
 * @brief Abstract class that implements the IRole interface. Since abstract this class cannot be instantiated.
 * @details It's important to note that an permission's name is camel case starting with lowercase letter.
 * @nosubgrouping
 */
abstract class AbstractRole implements IRole {
  
  protected $name;

  protected $permissions= [];


  public function __construct() {
    $this->name = lcfirst(preg_replace('/Role/', '', get_class()));
  }


  public function __toString() {
    return $this->getName();
  }


  public function getName() {
    return $this->name;
  }


  abstract public function getDescription();


  public function grantPermission($name, $class) {
    //$this->permissions[$operation->getName()] = $operation;
  }


  public function revokePermission($permissionName) {
    if ($this->permissionExists($permissionName))
      unset($this->permissions[$permissionName]);
  }


  public function permissionExists($permissionName) {
    return array_key_exists($permissionName, $this->permissions) ? TRUE : FALSE;
  }


  function obtainPermissionClass($name) {
    //! @todo: Implement obtainPermissionClass() method.
  }
}
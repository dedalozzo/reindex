<?php

/**
 * @file AbstractPermission.php
 * @brief This file contains the AbstractPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission;


use ReIndex\Security\User\IUser;
use ReIndex\Security\Role\IRole;

use Phalcon\Di;


/**
 * @brief Abstract class that implements the IPermission interface. Since abstract, this class cannot be instantiated.
 * @nosubgrouping
 */
abstract class AbstractPermission implements IPermission {

  /**
   * @var IRole $role
   */
  protected $role;

  /**
   * @var IUser $user
   */
  protected $user;

  /**
   * @var string $name
   */
  protected $name;

  /**
   * @var Di $di
   */
  protected $di;



  /**
   * @brief Constructor is protected so it can't call explicitly from outside.
   * @attention Subclasses must override this method and make it public.
   */
  protected function __construct() {
    $this->di = Di::getDefault();
    $this->user = $this->di['guardian']->getUser();
    $this->name = lcfirst(preg_replace('/Permission$/', '', get_class($this)));
  }


  /**
   * @brief Calls is triggered when invoking inaccessible methods in an object context.
   * @param[in] string $name The name of the method being called.
   * @param[in] array $arguments An enumerated array containing the parameters passed to the method.
   */
  public function __call($name, array $arguments) {
    if (is_callable($this->$name))
      return call_user_func($this->$name, $arguments);
    else
      throw new \RuntimeException("Method {$name} does not exist.");
  }


  /**
   * @brief Sometime a programmer needs to define a new special role, and eventually a set of permissions to check the
   * access, for this particular role, to any existent resource. Through this tecnique the programmer may define dynamic
   * methods to check the permissions for a particular role.
   * @param[in] string $name The name of the method being called.
   * @param[in] callable $value A closure.
   * @details In the following example we define the method `checkForGodRole`, to extend the `ImpersonatePermission`
   * class such as God will be able to impersonate anyone.
     @code
       // We assume `$member` is an instance of the `Member` class.
       $permission = new ImpersonatePermission($member);

       $permission->checkForGodRole = function() {
         return TRUE;
       };

       // Prints `true`.
       print $permission->checkForGodRole();
     @endcode
   */
  public function __set($name, $value) {
    $this->$name = is_callable($value) ? $value->bindTo($this, $this) : $value;
  }


  /**
   * @brief Overrides the magic method __toString() to return the permission's name.
   * @return string
   */
  public function __toString() {
    return $this->getName();
  }


  public function getName() {
    return $this->name;
  }


  abstract public function getDescription();


  public function setRole(IRole $role) {
    $this->role = $role;
  }


  public function getRole() {
    return $this->role;
  }

}
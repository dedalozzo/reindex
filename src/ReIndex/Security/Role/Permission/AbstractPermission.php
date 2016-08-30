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
   * @brief Constructor is protected so it can't call explicitly from outside. Subclasses must override this method.
   */
  protected function __construct() {
    $this->di = Di::getDefault();
    $this->user = $this->di['guardian']->getUser();
    $this->name = lcfirst(preg_replace('/Permission$/', '', get_class($this)));
  }


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
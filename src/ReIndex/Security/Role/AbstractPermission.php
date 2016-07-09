<?php

/**
 * @file AbstractPermission.php
 * @brief This file contains the AbstractPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role;


use ReIndex\Security\User\IUser;

use Phalcon\Di;


/**
 * @brief Abstract class that implements the IPermission interface. Since abstract, this class cannot be instantiated.
 * @nosubgrouping
 */
abstract class AbstractPermission implements IPermission {

  /**
   * @var IRole $role The execution role.
   */
  protected $role;

  /**
   * @var mixed $context The execution context.
   */
  protected $context;

  /**
   * @var Di $di
   */
  protected $di;

  /**
   * @var IUser $user
   */
  protected $user;

  /**
   * @var string $name
   */
  protected $name;


  public function __construct($context = NULL) {
    $this->context = $context;
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


  public function setContext($context) {
    $this->context = $context;
  }


  public function getContext() {
    return $this->context;
  }


  abstract public function check();


  public function castAs($newClass) {
    $obj = new $newClass;

    $obj->setRole($this->getRole());
    $obj->setContext($this->getContext());

    return $obj;
  }  

}
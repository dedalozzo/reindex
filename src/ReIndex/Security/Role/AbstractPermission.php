<?php

/**
 * @file AbstractPermission.php
 * @brief This file contains the AbstractPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role;


use Phalcon\Di;


/**
 * @brief Abstract class that implements the IPermission interface. Since abstract, this class cannot be instantiated.
 * @nosubgrouping
 */
abstract class AbstractPermission implements IPermission {
  protected $context; // Stores the execution context.
  protected $di; // Stores the default Dependency Injector.
  protected $user; // Stores the current user.
  protected $name; // Stores the permission's name.


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


  public function setContext($context) {
    $this->context = $context;
  }


  public function getContext() {
    return $this->context;
  }


  abstract public function check();


  public function castAs($newClass) {
    $obj = new $newClass;

    $obj->setContext($this->getContext());

    return $obj;
  }  

}
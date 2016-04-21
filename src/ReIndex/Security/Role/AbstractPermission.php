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

  protected $di; // Stores the default Dependency Injector.
  protected $user; // Stores the current user.
  protected $name; // Stores the permission's name.


  public function __construct() {
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


  abstract public function check();


  public function castAs($newClass) {
    $obj = new $newClass;
    
    foreach (get_object_vars($this) as $key => $name) {
      $obj->$key = $name;
    }
    
    return $obj;
  }  

}
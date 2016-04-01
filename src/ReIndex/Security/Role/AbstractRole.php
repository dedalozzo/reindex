<?php
/**
 * @file AbstractRole.php
 * @brief This file contains the AbstractRole class.
 * @details
 * @author Filippo F. Fadda
 */


//! Classes to describe roles
namespace ReIndex\Security\Role;


/**
 * @brief Abstract class that implements the IRole interface. Since abstract this class cannot be instantiated.
 * @nosubgrouping
 */
abstract class AbstractRole implements IRole {
  
  protected $name;


  public function __construct() {
    $this->name = preg_replace('/Role$/', '', get_class());
  }


  public function __toString() {
    return $this->getName();
  }


  public function getName() {
    return $this->name;
  }
  
  
  abstract public function getDescription();
  
}
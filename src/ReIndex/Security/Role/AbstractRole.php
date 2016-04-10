<?php
/**
 * @file AbstractRole.php
 * @brief This file contains the AbstractRole class.
 * @details
 * @author Filippo F. Fadda
 */


//! Classes to describe the roles a member can assume
namespace ReIndex\Security\Role;


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

}
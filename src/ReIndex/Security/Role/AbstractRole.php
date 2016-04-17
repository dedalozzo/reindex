<?php
/**
 * @file AbstractRole.php
 * @brief This file contains the AbstractRole class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role;


use ReIndex\Helper\ClassHelper;


/**
 * @brief Abstract class that implements the IRole interface. Since abstract this class cannot be instantiated.
 * @nosubgrouping
 */
abstract class AbstractRole implements IRole {
  
  protected $name;


  public function __construct() {
    // The role's name must be lowercase.
    $this->name = strtolower(preg_replace('/Role$/', '', ClassHelper::getClassName(get_class($this))));
  }


  public function __toString() {
    return $this->getName();
  }


  public function getName() {
    return $this->name;
  }


  abstract public function getDescription();

}
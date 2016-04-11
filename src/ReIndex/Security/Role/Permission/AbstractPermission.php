<?php
/**
 * @file AbstractPermission.php
 * @brief This file contains the AbstractPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission;


use Phalcon\Di;


abstract class AbstractPermission implements IPermission {

  protected $di;

  protected $user;

  protected $name;


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


  abstract public function check();

}
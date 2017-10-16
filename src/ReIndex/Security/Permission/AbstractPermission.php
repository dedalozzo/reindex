<?php

/**
 * @file Permission/AbstractPermission.php
 * @brief This file contains the AbstractPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission;


use Daikengo\Permission\AbstractPermission as Superclass;
use Daikengo\User\IUser;

use Phalcon\Di;


/**
 * @brief Abstract class that implements the IPermission interface. Since abstract, this class cannot be instantiated.
 * @nosubgrouping
 */
abstract class AbstractPermission extends Superclass {

  /**
   * @var IUser $user
   */
  protected $user;

  /**
   * @var Di $di
   */
  protected $di;



  /**
   * @brief Constructor is protected so it can't call explicitly from outside.
   * @attention Subclasses must override this method and make it public.
   */
  protected function __construct() {
    parent::__construct();
    $this->di = Di::getDefault();
    $this->user = $this->di['guardian']->getUser();
  }

}
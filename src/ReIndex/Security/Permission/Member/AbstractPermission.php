<?php

/**
 * @file Member/AbstractPermission.php
 * @brief This file contains the AbstractPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Members related permissions
namespace ReIndex\Security\Permission\Member;


use ReIndex\Security\Permission\AbstractPermission as Superclass;
use ReIndex\Doc\Member;


abstract class AbstractPermission extends Superclass {

  protected $member;


  /**
   * @brief Constructor.
   * @param[in] Doc::Member $member
   */
  public function __construct(Member $member) {
    $this->member = $member;
    parent::__construct();
  }

}
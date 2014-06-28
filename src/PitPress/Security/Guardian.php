<?php

//! @file Guardian.php
//! @brief This file contains the Guardian class.
//! @details
//! @author Filippo F. Fadda


//! PitPress security namespace.
namespace PitPress\Security;


use PitPress\Factory\UserFactory;


/**
 * @brief This class is responsible to ensure an user has the ability to perform any operation.
 * @nosubgrouping
 */
class Guardian {
  private $currentUser;


  /**
   * Creates an instance of the class.
   */
  public function __construct() {
    $this->currentUser = UserFactory::getFromCookie();
  }


  /**
   * @brief Returns the logged in user.
   * @return \PitPress\Model\User
   */
  public function getCurrentUser() {
    return $this->currentUser;
  }

} 
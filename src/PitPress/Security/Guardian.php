<?php

//! @file Guardian.php
//! @brief This file contains the Guardian class.
//! @details
//! @author Filippo F. Fadda


//! PitPress security namespace.
namespace PitPress\Security;


use PitPress\Factory\UserFactory;
use PitPress\Model\User;


/**
 * @brief This class is responsible to ensure an user has the ability to perform any operation.
 * @nosubgrouping
 */
class Guardian {

  const NO_USER_LOGGED_IN = -1; //!< No user logged in. The user is a guest.

  private static $initialized = FALSE;

  protected static $currentUser;


  public function __construct($config) {

    if (!self::$initialized) {
      self::$initialized = TRUE;
      self::$currentUser = UserFactory::getFromCookie();
    }

  }


  /**
   * @brief Returns the logged in user.
   * @return \PitPress\Model\User
   */
  public function getCurrentUser() {
    return self::$currentUser;
  }


  /**
   * @brief Returns `true` if the current visitor is just a guest.
   * @return bool
   */
  public function isGuest() {
    return is_null(self::$currentUser);
  }


  /**
   * @brief Impersonates the given user.
   * @param[in] \PitPress\Model\User $user
   */
  public function impersonate(User $user) {
    self::$currentUser = $user;
  }

}
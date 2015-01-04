<?php

//! @file Guardian.php
//! @brief This file contains the Guardian class.
//! @details
//! @author Filippo F. Fadda


//! PitPress security namespace.
namespace PitPress\Security;


use ElephantOnCouch\Extension;;

use PitPress\Factory\UserFactory;
use PitPress\Exception\NotEnoughPrivilegesException;


/**
 * @brief This class is responsible to ensure an user has the ability to perform any operation.
 * @nosubgrouping
 */
class Guardian {
  use Extension\TProperty;

  private static $initialized = FALSE;
  protected static $user = NULL;


  public function __construct($config) {

    if (!self::$initialized) {
      self::$initialized = TRUE;
      self::$user = UserFactory::getFromCookie();
    }
  }


  /**
   * @brief Returns the logged in user.
   * @return IUser
   */
  public function getUser() {
    return self::$user;
  }


  /**
   * @brief Impersonates the given user.
   * @param[in] IUser $user
   */
  public function impersonate(IUser $user) {

    if ((self::$user->isAdmin() && $user->isAdmin()) or
        (self::$user->isMember() && $user->isMember()) or
        (self::$user->isGuest()))
      throw new NotEnoughPrivilegesException('Non hai sufficienti privilegi per impersonare un altro utente.');
    else
      self::$user = $user;
  }

}
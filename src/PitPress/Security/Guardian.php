<?php

//! @file Guardian.php
//! @brief This file contains the Guardian class.
//! @details
//! @author Filippo F. Fadda


//! PitPress security namespace.
namespace PitPress\Security;


use ElephantOnCouch\Extension;
use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Factory\UserFactory;
use PitPress\Exception\NotEnoughPrivilegesException;
use PitPress\Security\User\IUser;


/**
 * @brief This class is responsible to ensure an user has the ability to perform any operation.
 * @nosubgrouping
 */
class Guardian {
  use Extension\TProperty;

  private static $initialized = FALSE;

  protected static $user = NULL;

  private $couch;


  public function __construct($config, $di) {

    if (!self::$initialized) {
      self::$initialized = TRUE;
      self::$user = UserFactory::fromCookie();
    }

    $this->couch = $di['couchdb'];
  }


  /**
   * @brief Returns the logged in user.
   * @return IUser
   */
  public function getUser() {
    return self::$user;
  }


  /**
   * @brief Returns `true` in case the username is taken, `false` otherwise.
   * @param[in] string $username The username.
   * @return bool
   */
  public function isTaken($username) {
    $opts = new ViewQueryOpts();
    $opts->setLimit(1)->setKey($username);

    $result = $this->couch->queryView("users", "byUsername", NULL, $opts);

    return ($result->isEmpty()) ? FALSE : TRUE;
  }


  /**
   * @brief Returns `true` is the current user can impersonate the specified user, `false` otherwise.
   * @details An admin can impersonate any member, but he can't impersonate another admin. A member (even an admin) can
   * impersonate a guest. No one can impersonate itself and a guest, of course, can't impersonate anyone.
   * @param[in] IUser $user
   * @return bool
   */
  private function canImpersonate(IUser $user) {
    if (self::$user->isAdmin() && $user->isMember() && !$user->isAdmin())
      return TRUE;
    elseif (self::$user->isMember() && $user->isGuest())
      return TRUE;
    else
      return FALSE;
  }


  /**
   * @brief Impersonates the given user.
   * @param[in] IUser $user
   */
  public function impersonate(IUser $user) {
    if ($this->canImpersonate($user))
      self::$user = $user;
    else
      throw new NotEnoughPrivilegesException('Non hai sufficienti privilegi per impersonare un altro utente.');
  }

}
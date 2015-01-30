<?php

//! @file FacebookConsumer.php
//! @brief This file contains the FacebookConsumer class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Security\Consumer;


use PitPress\Model\User;
use PitPress\Exception;


/**
 * @brief Facebook consumer implementation.
 * @nosubgrouping
 */
class FacebookConsumer extends OAuth2Consumer {

  /** @name Field Names */
  //!@{
  const ID = 'id';
  const EMAIL = 'email';
  const FIRST_NAME = 'first_name';
  const LAST_NAME = 'last_name';
  const GENDER = 'gender';
  const LOCALE = 'locale';
  const TIME_OFFSET = 'timezone';
  const PROFILE_URL = 'link';
  //!@}


  /**
   * @brief Facebook, like LinkedIn, doesn't provide a username, but PitPress needs one. So we guess the username using
   * first name and last name. In case the username has already been taken, adds a sequence number to the end.
   * @param[in] array $userData User data.
   * @return string
   */
  protected function guessUsername(array $userData) {
    $username = strtolower($userData[static::FIRST_NAME].$userData[static::LAST_NAME]);
    parent::guessUsername($username);
  }


  private function getGender($value) {
    return ($value === 'male') ? 'm' : 'f';
  }


  protected function update(User $user, array $userData) {
    $user->setMetadata('username', $this->guessUsername($userData), FALSE, FALSE);
    $user->setMetadata('firstName', $userData[static::FIRST_NAME], FALSE, FALSE);
    $user->setMetadata('lastName', $userData[static::LAST_NAME], FALSE, FALSE);
    $user->setMetadata('gender', $this->getGender(@$userData[static::GENDER]), FALSE, FALSE);
    $user->setMetadata('locale', @$userData[static::LOCALE], FALSE, FALSE);
    $user->setMetadata('timeOffset', @$userData[static::TIME_OFFSET], FALSE, FALSE);

    $user->addLogin($this->getName(), $userData[static::ID], $userData[static::EMAIL], $userData[static::PROFILE_URL]);
    $user->internetProtocolAddress = $_SERVER['REMOTE_ADDR'];
    //$user->save();
  }


  public function join() {
    $userData = $this->fetch('/me');
    $this->validate($userData);
    $this->consume($userData[static::ID], $userData[static::EMAIL], $userData);
  }


  public function getName() {
    return 'facebook';
  }


  public function getScope() {
    return ['email', 'user_friends'];
  }


  public function getFriends() {
    // /me/friends
  }

}
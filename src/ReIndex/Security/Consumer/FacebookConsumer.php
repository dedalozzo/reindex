<?php

/**
 * @file FacebookConsumer.php
 * @brief This file contains the FacebookConsumer class.
 * @details
 * @author Filippo F. Fadda
 */

namespace ReIndex\Security\Consumer;


use ReIndex\Model\Member;
use ReIndex\Exception;


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
   * @brief Facebook, like LinkedIn, doesn't provide a username, but ReIndex needs one. So we guess the username using
   * first name and last name. In case the username has already been taken, adds a sequence number to the end.
   * @param[in] array $userData Member data.
   * @retval string
   */
  private function guessUsername(array $userData) {
    $username = strtolower($userData[static::FIRST_NAME] . '.' . $userData[static::LAST_NAME]);
    return $this->buildUsername($username);
  }


  private function getGender($value) {
    return ($value === 'male') ? 'm' : 'f';
  }


  protected function update(Member $user, array $userData) {
    $user->setMetadata('username', $this->guessUsername($userData), FALSE, FALSE);
    $user->setMetadata('firstName', $userData[static::FIRST_NAME], FALSE, FALSE);
    $user->setMetadata('lastName', $userData[static::LAST_NAME], FALSE, FALSE);
    $user->setMetadata('gender', $this->getGender(@$userData[static::GENDER]), FALSE, FALSE);
    $user->setMetadata('locale', @$userData[static::LOCALE], FALSE, FALSE);
    $user->setMetadata('timeOffset', @$userData[static::TIME_OFFSET], FALSE, FALSE);

    parent::update($user, $userData);
  }


  /**
   * @brief Facebook is a trustworthy provider. This implementation returns `true`.
   * @retval bool
   */
  public function isTrustworthy() {
    return TRUE;
  }


  public function join() {
    $userData = $this->fetch('/me?fields=id,email,first_name,last_name,gender,locale,timezone,link');
    $this->validate($userData);
    $this->consume($userData[static::ID], $userData[static::EMAIL], $userData);
  }


  /**
   * @copydoc OAuth2Consumer::getName()
   */
  public function getName() {
    return 'facebook';
  }


  /**
   * @copydoc OAuth2Consumer::getScope()
   */
  public function getScope() {
    return ['email', 'user_friends'];
  }


  /**
   * @brief Returns the list of the user friends.
   * @retval array
   * @todo Implement Facebook.getFriends() method.
   */
  public function getFriends() {
    // /me/friends
  }

}
<?php

/**
 * @file GooglePlusConsumer.php
 * @brief This file contains the GooglePlusConsumer class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Security\Consumer;


use PitPress\Model\User;
use PitPress\Exception;


/**
 * @brief Google+ consumer implementation.
 * @nosubgrouping
 */
class GooglePlusConsumer extends OAuth2Consumer {

  /** @name Field Names */
  //!@{

  const ID = 'id';
  const EMAIL = 'email';
  const FIRST_NAME = 'first_name';
  const LAST_NAME = 'last_name';
  const GENDER = 'gender';
  const NICKNAME = 'nickname';
  const DISPLAY_NAME = 'displayName';
  const PROFILE_URL = 'url';

  //!@}


  private function extractPrimaryEmail($emails) {
    return $emails[0]['value'];
  }


  /**
   * @brief Google+, like Facebook, doesn't provide a username, but PitPress needs one. So we guess the username using
   * the user public profile url. In case the username has already been taken, adds a sequence number to the end.
   * @param[in] array $userData User data.
   * @retval string
   */
  private function guessUsername(array $userData) {
    if (isset($userData[static::NICKNAME]))
      $username = strtolower($userData[static::NICKNAME]);
    else
      $username = strtolower($userData[static::DISPLAY_NAME]);

    return $this->buildUsername($username);
  }


  /**
   * @copydoc OAuth2Consumer::update()
   */
  protected function update(User $user, array $userData) {
    $user->setMetadata('username', $this->guessUsername($userData), FALSE, FALSE);
    $user->setMetadata('firstName', @$userData['name']['givenName'], FALSE, FALSE);
    $user->setMetadata('lastName', @$userData['name']['familyName'], FALSE, FALSE);
    $user->setMetadata('birthday', @$userData['birthday'], FALSE, FALSE);
    $user->setMetadata('headline', @$userData['occupation'], FALSE, FALSE);
    $user->setMetadata('about', @$userData['aboutMe'], FALSE, FALSE);
    $user->setMetadata('profileUrl', @$userData['url'], FALSE, FALSE);

    parent::update($user, $userData);
  }


  /**
   * @brief Google+ is a trustworthy provider. This implementation returns `true`.
   * @retval bool
   */
  public function isTrustworthy() {
    return TRUE;
  }


  /**
   * @copydoc OAuth2Consumer::join()
   */
  public function join() {
    $userData = $this->fetch('https://www.googleapis.com/plus/v1/people/me/?fields=name(givenName,familyName),gender,birthday,url,occupation,aboutMe,displayName,emails/value');
    $userData['email'] = $this->extractPrimaryEmail($userData['emails']);
    $this->validate($userData);
    $this->consume($userData[static::ID], $userData[static::EMAIL], $userData);
  }


  /**
   * @copydoc OAuth2Consumer::getName()
   */
  public function getName() {
    return 'googleplus';
  }


  /**
   * @copydoc OAuth2Consumer::getScope()
   */
  public function getScope() {
    return ['email', 'profile'];
  }


  /**
   * @brief We don't care about Google+ list of friends, because Google+ is not widely used.
   * @retval array
   */
  public function getFriends() {
    return [];
  }

}
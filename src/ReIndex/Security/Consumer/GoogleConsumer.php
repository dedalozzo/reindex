<?php

/**
 * @file GoogleConsumer.php
 * @brief This file contains the GoogleConsumer class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Consumer;


use ReIndex\Model\Member;
use ReIndex\Exception;

use OAuth\OAuth2\Service\Google;


/**
 * @brief Google+ consumer implementation.
 * @nosubgrouping
 */
class GoogleConsumer extends OAuth2Consumer {

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
   * @brief Google+, like Facebook, doesn't provide a username, but ReIndex needs one. So we guess the username using
   * the user public profile url. In case the username has already been taken, adds a sequence number to the end.
   * @param[in] array $userData Member data.
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
  protected function update(Member $user, array $userData) {
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
    $userData = $this->fetch('people/me/?fields=name');
    //$userData = $this->fetch('people/me/?fields=name(givenName,familyName),nickname,displayName,gender,birthday,url,occupation,aboutMe,emails/value');
    $userData['email'] = $this->extractPrimaryEmail($userData['emails']);
    $this->validate($userData);
    $this->consume($userData[static::ID], $userData[static::EMAIL], $userData);
  }


  /**
   * @copydoc OAuth2Consumer::getName()
   */
  public function getName() {
    return 'google';
  }


  /**
   * @copydoc OAuth2Consumer::getScope()
   */
  public function getScope() {
    return [Google::SCOPE_EMAIL, Google::SCOPE_PROFILE];
  }


  /**
   * @brief We don't care about Google+ list of friends, because Google+ is not widely used.
   * @retval array
   */
  public function getFriends() {
    return [];
  }

}
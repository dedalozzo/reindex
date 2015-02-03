<?php

//! @file LinkedInConsumer.php
//! @brief This file contains the LinkedInConsumer class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Security\Consumer;


use PitPress\Model\User;
use PitPress\Exception\InvalidFieldException;


/**
 * @brief LinkedIn consumer implementation.
 * @nosubgrouping
 */
class LinkedInConsumer extends OAuth2Consumer {

  /** @name Field Names */
  //!@{
  const ID = 'id';
  const EMAIL = 'emailAddress';
  const FIRST_NAME = 'firstName';
  const LAST_NAME = 'lastName';
  const BIRTHDAY = 'dateOfBirth';
  const HEADLINE = 'headline';
  const ABOUT = 'firstName';
  const PROFILE_URL = 'publicProfileUrl';
  //!@}


  /**
   * @brief LinkedIn, like Facebook, doesn't provide a username, but PitPress needs one. So we guess the username using
   * the user public profile url. In case the username has already been taken, adds a sequence number to the end.
   * @param[in] array $userData User data.
   * @return string
   */
  private function guessUsername(array $userData) {
    if (preg_match('%.+/in/(?P<username>.+)%i', $userData[static::PROFILE_URL], $matches))
      $username = $matches['username'];
    else
      $username = strtolower($userData[static::FIRST_NAME].$userData[static::LAST_NAME]);

    return $this->buildUsername($username);
  }


  protected function onAuthorizationGranted() {
    // Retrieves the CSRF state parameter.
    $state = isset($_GET['state']) ? $_GET['state'] : NULL;

    // This was a callback request from LinkedIn, get the token.
    $token = $this->service->requestAccessToken($_GET['code'], $state);
  }


  protected function update(User $user, array $userData) {
    $user->setMetadata('username', $this->guessUsername($userData), FALSE, FALSE);
    $user->setMetadata('firstName', $userData[static::FIRST_NAME], FALSE, FALSE);
    $user->setMetadata('lastName', $userData[static::LAST_NAME], FALSE, FALSE);
    $user->setMetadata('birthday', @$userData[static::BIRTHDAY], FALSE, FALSE);
    $user->setMetadata('headline', @$userData[static::HEADLINE], FALSE, FALSE);
    $user->setMetadata('about', @$userData[static::ABOUT], FALSE, FALSE);

    parent::update($user, $userData);
  }


  /**
   * @brief LinkedIn is a trustworthy provider. This implementation returns `true`.
   * @return bool
   */
  public function isTrustworthy() {
    return TRUE;
  }


  public function join() {
    $userData = $this->fetch('/people/~:(id,email-address,first-name,last-name,public-profile-url,headline,summary,date-of-birth)?format=json');
    $this->validate($userData);
    $this->consume($userData[static::ID], $userData[static::EMAIL], $userData);
  }


  public function getName() {
    return 'linkedin';
  }


  public function getScope() {
    return ['r_fullprofile', 'r_emailaddress', 'r_contactinfo', 'r_network'];
  }


  public function getFriends() {

  }

}
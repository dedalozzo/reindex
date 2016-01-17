<?php

/**
 * @file LinkedInConsumer.php
 * @brief This file contains the LinkedInConsumer class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Consumer;


use ReIndex\Model\Member;
use ReIndex\Exception\InvalidFieldException;


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
   * @brief LinkedIn, like Facebook, doesn't provide a username, but ReIndex needs one. So we guess the username using
   * the user public profile url. In case the username has already been taken, adds a sequence number to the end.
   * @param[in] array $userData Member data.
   * @retval string
   */
  private function guessUsername(array $userData) {
    if (preg_match('%.+/in/(?P<username>.+)%i', $userData[static::PROFILE_URL], $matches))
      $username = $matches['username'];
    else
      $username = strtolower($userData[static::FIRST_NAME].$userData[static::LAST_NAME]);

    return $this->buildUsername($username);
  }


  /**
   * @copydoc OAuth2Consumer::onAuthorizationGranted()
   */
  protected function onAuthorizationGranted() {
    // Retrieves the CSRF state parameter.
    $state = isset($_GET['state']) ? $_GET['state'] : NULL;

    // This was a callback request from LinkedIn, get the token.
    $token = $this->service->requestAccessToken($_GET['code'], $state);
  }


  /**
   * @copydoc OAuth2Consumer::update()
   */
  protected function update(Member $user, array $userData) {
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
   * @retval bool
   */
  public function isTrustworthy() {
    return TRUE;
  }


  /**
   * @copydoc OAuth2Consumer::join()
   */
  public function join() {
    $userData = $this->fetch('/people/~:(id,email-address,first-name,last-name,public-profile-url,headline,summary,date-of-birth)?format=json');
    $this->validate($userData);
    $this->consume($userData[static::ID], $userData[static::EMAIL], $userData);
  }


  /**
   * @copydoc OAuth2Consumer::getName()
   */
  public function getName() {
    return 'linkedin';
  }


  /**
   * @copydoc OAuth2Consumer::getScope()
   */
  public function getScope() {
    // As of May 15, LinkedIn locked down the API and restricted the usage to an extremely limited set of access points.
    // @see http://stackoverflow.com/a/30364596/1889828
    // @see https://developer.linkedin.com/support/developer-program-transition#troubleshooting
    // So we cannot longer ask for the full profile or the network.
    //return ['r_fullprofile', 'r_emailaddress', 'r_contactinfo', 'r_network'];

    return ['r_basicprofile', 'r_emailaddress'];
  }


  /**
   * @brief Returns the list of user connections.
   * @retval array
   * @todo Implement LinkedIn.getFriends() method.
   */
  public function getFriends() {

  }

}
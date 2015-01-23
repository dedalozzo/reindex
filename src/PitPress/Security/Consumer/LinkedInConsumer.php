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


  // LinkedIn, like Facebook, doesn't provide a username, but PitPress needs one. So we guess the username using the
  // user public profile url. In case the username has already been taken, we add a sequence number to the end.
  private function guessUsername($publicProfileUrl) {
    if (preg_match('%.+/in/(?P<username>.+)%i', $publicProfileUrl, $matches))
      return $matches['username'];
    else
      throw new InvalidFieldException("Le informazioni fornite da LinkedIn sono incomplete.");
  }


  protected function update(User $user, array $userData) {
    $user->setMetadata('username', $this->guessUsername($userData['publicProfileUrl']), FALSE, FALSE);
    $user->setMetadata('email', @$userData['emailAddress'], $this->canReplacePrimaryEmail($user), FALSE);
    $user->setMetadata('firstName', @$userData['firstName'], FALSE, FALSE);
    $user->setMetadata('lastName', @$userData['lastName'], FALSE, FALSE);
    $user->setMetadata('birthday', @$userData['dateOfBirth'], FALSE, FALSE);
    $user->setMetadata('headline', @$userData['headline'], FALSE, FALSE);
    $user->setMetadata('about', @$userData['summary'], FALSE, FALSE);
    $user->setMetadata('profileUrl', @$userData['publicProfileUrl'], FALSE, FALSE);
    $user->setMetadata('headline', @$userData['headline'], FALSE, FALSE);

    $user->addLogin($this->getName(), $userData['id'], $userData['emailAddress'], $userData['publicProfileUrl']);
    $user->internetProtocolAddress = $_SERVER['REMOTE_ADDR'];
    $user->save();
  }


  public function join() {
    $userData = $this->fetch('/people/~:(id,email-address,first-name,last-name,public-profile-url,headline,summary,date-of-birth)?format=json');
    $this->validate('id', 'emailAddress', $userData);
    $this->consume($userData['id'], $userData['emailAddress'], $userData);
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
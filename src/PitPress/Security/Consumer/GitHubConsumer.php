<?php

//! @file GitHubConsumer.php
//! @brief This file contains the GitHubConsumer class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Security\Consumer;


use PitPress\Model\User;


/**
 * @brief GitHub consumer implementation.
 * @nosubgrouping
 */
class GitHubConsumer extends OAuth2Consumer {


  protected function update(User $user, array $userData) {
    // id
    // login
    // emails[0]
    // company
    // bio
    // html_url
    // dateOfBirth [none]
    // gender [none]
    // updated_at
    // firstName (generated from name)
    // lastName (generated from name)
    // locale [none]
    // timeOffset [none]

    $user->setMetadata('username', $this->guessUsername($userData['publicProfileUrl']), FALSE, FALSE);
    $user->setMetadata('email', @$userData['emailAddress'], FALSE, FALSE);
    $user->setMetadata('firstName', @$userData['firstName'], FALSE, FALSE);
    $user->setMetadata('lastName', @$userData['lastName'], FALSE, FALSE);
    $user->setMetadata('birthday', @$userData['dateOfBirth'], FALSE, FALSE);
    $user->setMetadata('headline', @$userData['headline'], FALSE, FALSE);
    $user->setMetadata('about', @$userData['summary'], FALSE, FALSE);
    $user->setMetadata('profileUrl', @$userData['publicProfileUrl'], FALSE, FALSE);
    $user->setMetadata('headline', @$userData['headline'], FALSE, FALSE);

    $user->addLogin($this->getName(), $userData['id'], $userData['publicProfileUrl']);
    $user->internetProtocolAddress = $_SERVER['REMOTE_ADDR'];
    $user->save();
  }


  public function join() {
    $userData = $this->fetch('/people/~:(id,email-address,first-name,last-name,public-profile-url,headline,summary,date-of-birth)?format=json');
    $this->validate('id', 'emailAddress', $userData);
    $this->consume($userData['id'], $userData['emailAddress'], $userData);
  }


  public function getName() {
    return 'github';
  }


  public function getScope() {
    return [];
  }

}
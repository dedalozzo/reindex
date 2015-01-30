<?php

//! @file GitHubConsumer.php
//! @brief This file contains the GitHubConsumer class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Security\Consumer;


use PitPress\Model\User;
use PitPress\Helper\Text;


/**
 * @brief GitHub consumer implementation.
 * @nosubgrouping
 */
class GitHubConsumer extends OAuth2Consumer {

  /** @name Field Names */
  //!@{
  const ID = 'id';
  const EMAIL = 'email';
  const LOGIN = 'login';
  const FIRST_NAME = 'first';
  const LAST_NAME = 'last';
  const HEADLINE = 'company';
  const ABOUT = 'bio';
  const PROFILE_URL = 'html_url';
  //!@}


  private function extractPrimaryEmail($emails) {

  }


  protected function update(User $user, array $userData) {
    $user->setMetadata('username', $this->guessUsername($userData[static::LOGIN]), FALSE, FALSE);

    $names = Text::splitFullName(@$userData['name']);
    $user->setMetadata('firstName', @$names[static::FIRST_NAME], FALSE, FALSE);
    $user->setMetadata('lastName', @$names[static::LAST_NAME], FALSE, FALSE);

    $user->setMetadata('headline', @$userData[static::HEADLINE], FALSE, FALSE);
    $user->setMetadata('about', @$userData[static::ABOUT], FALSE, FALSE);
    $user->setMetadata('profileUrl', @$userData[static::PROFILE_URL], FALSE, FALSE);

    $user->addLogin($this->getName(), $userData[static::ID], $userData[static::EMAIL], $userData[static::PROFILE_URL]);
    $user->internetProtocolAddress = $_SERVER['REMOTE_ADDR'];
    $user->save();
  }


  public function join() {
    $userData = $this->fetch('/user/');

    $emails = $this->fetch('/user/emails~:(id,emaillogin,name,company,bio,html-url)?format=json');
    $userData['email'] = $this->extractPrimaryEmail($emails);

    $this->validate('id', 'email', $userData);
    $this->consume($userData[static::ID], $userData[static::EMAIL], $userData);
  }


  public function getName() {
    return 'github';
  }


  public function getScope() {
    return ['user', 'public_repo'];
  }


  public function getFriends() {

  }

}
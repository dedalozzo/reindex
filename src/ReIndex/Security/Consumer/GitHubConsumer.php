<?php

/**
 * @file GitHubConsumer.php
 * @brief This file contains the GitHubConsumer class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Consumer;


use ReIndex\Model\User;
use ReIndex\Helper\Text;


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
    // GitHub should return a list of e-mails and for each one specify if the e-mail is primary and verified. But
    // unfortunately the API doesn't work like expected.

    /*
    $address = NULL;

    foreach ($emails as $email)
      if ($email['verified'] && $email['primary']) {
        $address = $email['email'];
        break;
      }

    return $address;
    */

    return $emails[0];
  }


  /**
   * @copydoc OAuth2Consumer::update()
   */
  protected function update(User $user, array $userData) {
    $user->setMetadata('username', $this->buildUsername($userData[static::LOGIN]), FALSE, FALSE);

    $names = Text::splitFullName(@$userData['name']);
    $user->setMetadata('firstName', @$names[static::FIRST_NAME], FALSE, FALSE);
    $user->setMetadata('lastName', @$names[static::LAST_NAME], FALSE, FALSE);

    $user->setMetadata('headline', @$userData[static::HEADLINE], FALSE, FALSE);
    $user->setMetadata('about', @$userData[static::ABOUT], FALSE, FALSE);

    parent::update($user, $userData);
  }


  /**
   * @brief GitHub can't be trust! This implementation returns `false`.
   * @retval bool
   * @warning GitHub let you login using an e-mail that hasn't been verified.
   */
  public function isTrustworthy() {
    return FALSE;
  }


  public function join() {
    $userData = $this->fetch('/user');
    $emails = $this->fetch('/user/emails');
    $userData['email'] = $this->extractPrimaryEmail($emails);
    $this->validate($userData);
    $this->consume($userData[static::ID], $userData[static::EMAIL], $userData);
  }


  public function getName() {
    return 'github';
  }


  public function getScope() {
    return ['user', 'public_repo'];
  }


  /**
   * @brief GitHub doesn't provide a list of friends.
   * @retval array
   */
  public function getFriends() {
    return [];
  }

}
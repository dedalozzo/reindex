<?php

/**
 * @file User.php
 * @brief This file contains the User class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model;


use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Extension;
use PitPress\Exception;
use PitPress\Security\User\IUser;
use PitPress\Security\User\System;
use PitPress\Security\Provider\IProvider;


/**
 * @brief This class is used to represent a registered user.
 * @nosubgrouping
 */
class User extends Storable implements IUser, Extension\ICount {
  use Extension\TCount;


  /**
   * @brief Searches for the user identified by the specified email, if any returns it, otherwise return `false`.
   * @param[in] IProvider $provider The provider instance.
   */
  public function addProvider(IProvider $provider) {
    // todo
  }


  /**
   * @brief Removes the specified provider and all its information.
   * @param[in] string $name The Provider's name
   */
  public function removeProvider($name) {
    // todo
  }


  /**
   * @brief Given a e-mail, returns the gravatar URL for the corresponding user.
   * @param[in] string $email The user e-mail.
   * @return string
   */
  public static function getGravatar($email) {
    return 'http://gravatar.com/avatar/'.md5(mb_strtolower($email, 'utf-8')).'?d=identicon';
  }


  /**
   * @brief Returns the user's favorite tags if any.
   * @return array
   */
  public function getFavoriteTags() {
    $opts = new ViewQueryOpts();
    $opts->setKey($this->getId())->doNotReduce();
    $favorites = $this->couch->queryView("favorites", "byUserTags", NULL, $opts);

    if ($favorites->isEmpty())
      return [];

    $opts->reset();
    $opts->doNotReduce();
    return $this->couch->queryView("tags", "allNames", array_column($favorites->asArray(), 'value'), $opts)->asArray();
  }


  /**
   * @brief Returns the actual user's age based on his birthday, `null`in case a the user's birthday is not available.
   * @return int|null
   */
  public function getAge() {
    if ($this->issetBirthday()) {
      $now = new \DateTime();
      $birthdayTimestamp = $this->getBirthday();
      $birthday = new \DateTime("@$birthdayTimestamp");
      return $now->diff($birthday)->y;
    }
    else
      return NULL;
  }


  /**
   * @brief Last time the user has logged in.
   * @return string The time expressed as `3 Aprile, 2013` or an empty string.
   */
  public function getLastVisit() {
    if (isset($this->meta['lastVisit']))
      return strftime('%e %B, %Y', $this->meta['lastVisit']);
    else
      return "";
  }


  /**
   * @brief Returns the elapsed time since the user registration.
   * @return string
   */
  public function getElapsedTimeSinceRegistration() {
    return strftime('%e %B, %Y', $this->createdAt);
  }


  /**
   * @brief Returns the user's reputation.
   * @return integer
   */
  public function getReputation() {
    $opts = new ViewQueryOpts();
    $opts->setKey([$this->id]);

    $reputation = $this->couch->queryView("reputation", "perUser", NULL, $opts)->getReducedValue();

    if ($reputation > 1)
      return $reputation;
    else
      return 1;
  }


  /** @name Confirmation Methods */
  //!@{

  /**
   * @brief Confirm the user.
   */
  public function confirm() {
    $this->meta['confirmed'] = TRUE;
  }


  /**
   * @copydoc IUser::isConfirmed()
   */
  public function isConfirmed() {
    return isset($this->meta['confirmed']);
  }

  //!@}


  /** @name Access Control Management Methods */
  //!@{

  /**
   * @brief Grants the administrator privileges.
   * @attention Only the System user can grant the administrator privileges.
   */
  public function grantAdmin() {
    if ($this->user instanceof System)
      $this->meta['admin'] = TRUE;
    else
      throw new Exception\NotEnoughPrivilegesException("I privilegi di amministratore possono essere assegnati dall'utente di sistema.");
  }


  /**
   * @brief Revokes the administrator privileges.
   * @attention Only the System user can revoke the administrator privileges.
   */
  public function revokeAdmin() {
    if ($this->user instanceof System) {
      if ($this->isMetadataPresent('admin'))
        unset($this->meta['admin']);
    }
    else
      throw new Exception\NotEnoughPrivilegesException("I privilegi di amministratore possono essere revocati dall'utente di sistema.");
  }


  /**
   * @brief Grants the moderator privileges.
   * @attention Only an admin can grant the moderator privileges (or System of course).
   */
  public function grantModerator() {
    if ($this->user->isAdmin() or $this->user instanceof System)
      $this->meta['moderator'] = TRUE;
    else
      throw new Exception\NotEnoughPrivilegesException("Privilegi di accesso insufficienti.");
  }


  /**
   * @brief Revokes the moderator privileges.
   * @attention Only an admin can revoke the moderator privileges (or System of course).
   */
  public function revokeModerator() {
    if ($this->user->isAdmin() or $this->user instanceof System) {
      if ($this->isMetadataPresent('moderator'))
        unset($this->meta['moderator']);
    }
    else
      throw new Exception\NotEnoughPrivilegesException("Privilegi di accesso insufficienti.");
  }


  /**
   * @brief Grants the reviewer privileges.
   */
  public function grantReviewer() {
    $this->meta['reviewer'] = TRUE;
  }


  /**
   * @brief Revokes the reviewer privileges.
   */
  public function revokeReviewer() {
    if ($this->isMetadataPresent('reviewer'))
      unset($this->meta['reviewer']);
  }


  /**
   * @brief Grants the editor privileges.
   */
  public function grantEditor() {
    $this->meta['editor'] = TRUE;
  }


  /**
   * @brief Revokes the editor privileges.
   */
  public function revokeEditor() {
    if ($this->isMetadataPresent('editor'))
      unset($this->meta['editor']);
  }


  /**
   * @brief Revokes all privileges.
   */
  public function revokeAll() {
    $this->revokeAdmin();
    $this->revokeModerator();
    $this->revokeReviewer();
    $this->revokeEditor();
  }


  /**
   * @brief This implementation returns always `false`.
   * @return bool
   */
  public function isGuest() {
    return FALSE;
  }


  /**
   * @brief This implementation returns always `true`.
   * @return bool
   */
  public function isMember() {
    return TRUE;
  }


  /**
   * @copydoc IUser::isAdmin()
   */
  public function isAdmin() {
    return isset($this->meta['admin']);
  }


  /**
   * @copydoc IUser::isModerator()
   */
  public function isModerator() {
    return $this->isAdmin() or isset($this->meta['moderator']);
  }


  /**
   * @copydoc IUser::isReviewer()
   */
  public function isReviewer() {
    return $this->isModerator() or isset($this->meta['reviewer']);
  }


  /**
   * @copydoc IUser::isEditor()
   */
  public function isEditor() {
    return $this->isReviewer() or isset($this->meta['editor']);
  }

  //!@}


  /** @name Ban Management Methods */
  //!@{


  /**
   * @brief Returns `true` if the ban is expired, otherwise `false`.
   * @return bool
   */
  protected function isBanExpired() {
    if ($this->isMetadataPresent('bannedFor') == 'ever') {
      return FALSE;
    }
    else {
      $expireOn = (new \DateTime())->setTimestamp($this->meta['bannedOn'])->add(sprintf('P%dD', $this->meta['bannedFor']))->getTimestamp();
      return (time() > $expireOn) ? TRUE : FALSE;
    }
  }


  /**
   * @brief Bans the user.
   * @param[in] integer $days The ban duration in days. When zero, the ban is permanent.
   */
  public function ban($days = 0) {
    $this->meta['banned'] = TRUE;
    $this->meta['bannedOn'] = time();

    if ($days)
      $this->meta['bannedFor'] = $days;
    else
      $this->meta['bannedFor'] = 'ever';

    $this->save();
  }


  /**
   * @brief Removes the ban.
   */
  public function unban() {
    if ($this->isMetadataPresent('banned')) {
      unset($this->meta['banned']);
      unset($this->meta['bannedOn']);
      unset($this->meta['bannedFor']);
    }

    $this->save();
  }


  /**
   * @brief Returns `true` if the user has been banned.
   * @details When expired, removes the ban.
   * @return bool
   */
  public function isBanned() {
    if ($this->isMetadataPresent('banned')) {

      if ($this->isBanExpired()) {
        $this->unban();
        return FALSE;
      }
      else // It's a permanent ban.
        return TRUE;

    }
    else
      return FALSE;
  }

  //!@}


  //! @cond HIDDEN_SYMBOLS

  public function getFirstName() {
    return $this->meta['firstName'];
  }


  public function issetFirstName() {
    return isset($this->meta['firstName']);
  }


  public function setFirstName($value) {
    $this->meta['firstName'] = $value;
  }


  public function unsetFirstName() {
    if ($this->isMetadataPresent('firstName'))
      unset($this->meta['firstName']);
  }


  public function getLastName() {
    return $this->meta['lastName'];
  }


  public function issetLastName() {
    return isset($this->meta['lastName']);
  }


  public function setLastName($value) {
    $this->meta['lastName'] = $value;
  }


  public function unsetLastName() {
    if ($this->isMetadataPresent('lastName'))
      unset($this->meta['lastName']);
  }


  public function getUsername() {
    return $this->meta['username'];
  }


  public function issetUsername() {
    return isset($this->meta['username']);
  }


  public function setUsername($value) {
    $this->meta['username'] = $value;
  }


  public function unsetUsername() {
    if ($this->isMetadataPresent('username'))
      unset($this->meta['username']);
  }


  public function getEmail() {
    return $this->meta['email'];
  }


  public function issetEmail() {
    return isset($this->meta['email']);
  }


  public function setEmail($value) {
    $this->meta['email'] = strtolower($value);
  }


  public function unsetEmail() {
    if ($this->isMetadataPresent('email'))
      unset($this->meta['email']);
  }


  public function getPassword() {
    return $this->meta['password'];
  }


  public function issetPassword() {
    return isset($this->meta['password']);
  }


  public function setPassword($value) {
    $this->meta['password'] = $value;
  }


  public function unsetPassword() {
    if ($this->isMetadataPresent('password'))
      unset($this->meta['password']);
  }


  public function getGender() {
    return $this->meta['gender'];
  }


  public function issetGender() {
    return isset($this->meta['gender']);
  }


  public function setGender($value) {
    $this->meta['gender'] = $value;
  }


  public function unsetGender() {
    if ($this->isMetadataPresent('gender'))
      unset($this->meta['gender']);
  }


  public function getBirthday() {
    return $this->meta['birthday'];
  }


  public function issetBirthday() {
    return isset($this->meta['birthday']);
  }


  public function setBirthday($value) {
    $this->meta['birthday'] = $value;
  }


  public function unsetBirthday() {
    if ($this->isMetadataPresent('birthday'))
      unset($this->meta['birthday']);
  }


  public function getAbout() {
    return $this->meta['about'];
  }


  public function issetAbout() {
    return isset($this->meta['about']);
  }


  public function setAbout($value) {
    $this->meta['about'] = $value;
  }

  
  public function unsetAbout() {
    if ($this->isMetadataPresent('about'))
      unset($this->meta['about']);
  }
  

  public function getInternetProtocolAddress() {
    return $this->meta['ipAddress'];
  }


  public function issetInternetProtocolAddress() {
    return isset($this->meta['ipAddress']);
  }


  public function setInternetProtocolAddress($value) {
    $this->meta['ipAddress'] = $value;
  }


  public function unsetInternetProtocolAddress() {
    if ($this->isMetadataPresent('ipAddress'))
      unset($this->meta['ipAddress']);
  }


  public function getHash() {
    return $this->meta['hash'];
  }


  public function issetHash() {
    return isset($this->meta['hash']);
  }


  public function setHash($value) {
    $this->meta['hash'] = $value;
  }


  public function unsetHash() {
    if ($this->isMetadataPresent('hash'))
      unset($this->meta['hash']);
  }

  
  public function getLocale() {
    return $this->meta['locale'];
  }


  public function issetLocale() {
    return isset($this->meta['locale']);
  }


  public function setLocale($value) {
    $this->meta['locale'] = $value;
  }


  public function unsetLocale() {
    if ($this->isMetadataPresent('locale'))
      unset($this->meta['locale']);
  }


  public function getTimeOffset() {
    return $this->meta['timeOffset'];
  }


  public function issetTimeOffset() {
    return isset($this->meta['timeOffset']);
  }


  public function setTimeOffset($value) {
    $this->meta['timeOffset'] = $value;
  }


  public function unsetTimeOffset() {
    if ($this->isMetadataPresent('timeOffset'))
      unset($this->meta['timeOffset']);
  }


  public function getFacebookId() {
    return $this->meta['facebookId'];
  }


  public function issetFacebookId() {
    return isset($this->meta['facebookId']);
  }


  public function setFacebookId($value) {
    $this->meta['facebookId'] = $value;
  }


  public function unsetFacebookId() {
    if ($this->isMetadataPresent('facebookId'))
      unset($this->meta['facebookId']);
  }


  public function getGithubId() {
    return $this->meta['githubId'];
  }


  public function issetGithubId() {
    return isset($this->meta['githubId']);
  }


  public function setGithubId($value) {
    $this->meta['githubId'] = $value;
  }


  public function unsetGithubId() {
    if ($this->isMetadataPresent('githubId'))
      unset($this->meta['githubId']);
  }

  //! @endcond

}
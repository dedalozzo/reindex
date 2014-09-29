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


/**
 * @brief This class is used to represent a registered user.
 * @nosubgrouping
 */
class User extends Storable implements Extension\ICount {
  use Extension\TCount;


  /**
   * @brief Given a e-mail, returns the gravatar URL for the corresponding user.
   * @param[in] string $email The user e-mail.
   * @return string
   */
  public static function getGravatar($email) {
    return 'http://gravatar.com/avatar/'.md5(mb_strtolower($email, 'utf-8')).'?d=identicon';
  }


  /**
   * @brief Returns the actual user's age based on his birthday, `null`in case a the user's birthday is not available.
   * @return byte|null
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
   * @brief Returns `true` if the user has been confirmed.
   */
  public function isConfirmed() {
    return isset($this->meta['confirmed']);
  }

  //!@}


  /** @name Access Control Management Methods */
  //!@{

  /**
   * @brief Promotes the user to administrator.
   */
  public function setAsAdmin() {
    $this->unsetAsModerator();
    $this->meta['admin'] = TRUE;
  }


  /**
   * @brief Reverts the administrator to a normal user.
   */
  public function unsetAsAdmin() {
    if ($this->isMetadataPresent('admin'))
      unset($this->meta['admin']);
  }


  /**
   * @brief Returns `true` in case the user is an administrator.
   */
  public function isAdmin() {
    return isset($this->meta['admin']);
  }


  /**
   * @brief Promotes the user to moderator.
   */
  public function setAsModerator() {
    if (!$this->isAdmin())
      $this->meta['moderator'] = TRUE;
  }


  /**
   * @brief Reverts the moderator to a normal user.
   */
  public function unsetAsModerator() {
    if ($this->isMetadataPresent('moderator'))
      unset($this->meta['moderator']);
  }


  /**
   * @brief Returns `true` in case the user is a moderator.
   */
  public function isModerator() {
    return isset($this->meta['moderator']);
  }

  //!@}


  /** @name Ban Management Methods */
  //!@{

  /**
   * @brief Bans the user.
   * @param[in] integer $days The ban duration in days.
   */
  public function ban($days) {
    $this->meta['bannedOn'] = time();
    $this->meta['bannedFor'] = $days;
    $this->meta['banned'] = TRUE;
  }


  /**
   * @brief Removes the ban.
   */
  public function unban() {
    if ($this->isMetadataPresent('banned'))
      unset($this->meta['banned']);
  }


  /**
   * @brief Returns `true` if the user has been banned.
   */
  public function isBanned() {
    return isset($this->meta['banned']);
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

  //! @endcond

}
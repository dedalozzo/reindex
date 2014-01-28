<?php

//! @file User.php
//! @brief This file contains the User class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\User;


use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Model\Storable;
use PitPress\Extension;


//! @brief This class is used to represent a registered user.
//! @nosubgrouping
class User extends Storable implements Extension\ICount {
  use Extension\TCount;


  //! @brief Given a e-mail, returns the gravatar URL for the corresponding user.
  //! @param[in] string $email The user e-mail.
  //! @return string
  public static function getGravatar($email) {
    return 'http://gravatar.com/avatar/'.md5(strtolower($email)).'?d=identicon';
  }


  //! @brief Returns the actual user's age based on his birthday, `null`in case a the user's birthday is not available.
  //! @return byte|null
  public function getAge() {
    if ($this->issetBirthday()) {
      $now = new \DateTime();
      $birthday = new \DateTime("@$this->getBirthday()");
      return $now->diff($birthday)->y;
    }
    else
      return NULL;
  }


  //! @brief Last time the user has logged in.
  public function getLastVisit($value) {
    $this->meta['lastVisit'] = $value;
  }


  //! @brief Returns the user's reputation.
  //! @return integer
  public function getReputation() {
    $opts = new ViewQueryOpts();
    $opts->setKey([$this->id]);

    $result = $this->couch->queryView("reputation", "perUser", NULL, $opts);

    if (!empty($result['rows'])) {
      $reputation =  $result['rows'][0]['value'];

      if ($reputation > 1)
        return $reputation;
      else
        return 1;
    }
    else
      return 1;
  }


  //! @name Confirmation Methods
  // @{

  //! @brief Confirm the user.
  public function confirm() {
    $this->meta['confirmed'] = "true";
  }


  //! @brief Returns `true` if the user has been confirmed.
  public function isConfirmed() {
    return isset($this->meta['confirmed']);
  }

  //@}


  //! @name Ban Management Methods
  // @{

  //! @brief Bans the user.
  //! @param[in] integer $days The ban duration in days.
  public function ban($days) {
    $this->meta['bannedOn'] = time();
    $this->meta['bannedFor'] = $days;
    $this->meta['banned'] = "true";
  }


  //! @brief Removes the ban.
  public function unban() {
    if ($this->isMetadataPresent('banned'))
      unset($this->meta['banned']);
  }


  //! @brief Returns `true` if the user has been banned.
  public function isBanned() {
    return isset($this->meta['banned']);
  }

  //@}


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


  public function getLastName() {
    return $this->meta['lastName'];
  }


  public function issetLastName() {
    return isset($this->meta['lastName']);
  }


  public function setLastName($value) {
    $this->meta['lastName'] = $value;
  }


  public function getDisplayName() {
    return $this->meta['displayName'];
  }


  public function issetDisplayName() {
    return isset($this->meta['displayName']);
  }


  public function setDisplayName($value) {
    $this->meta['displayName'] = $value;
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


  public function getPassword() {
    return $this->meta['password'];
  }


  public function issetPassword() {
    return isset($this->meta['password']);
  }


  public function setPassword($value) {
    $this->meta['password'] = $value;
  }


  public function getSex() {
    return $this->meta['sex'];
  }


  public function issetSex() {
    return isset($this->meta['sex']);
  }


  public function setSex($value) {
    $this->meta['sex'] = $value;
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


  public function getAbout() {
    return $this->meta['about'];
  }


  public function issetAbout() {
    return isset($this->meta['about']);
  }


  public function setAbout($value) {
    $this->meta['about'] = $value;
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


  public function getCreationDate() {
    return $this->meta['creationDate'];
  }


  public function issetCreationDate() {
    return isset($this->meta['creationDate']);
  }


  public function setCreationDate($value) {
    $this->meta['creationDate'] = $value;
  }


  public function unsetCreationDate() {
    if ($this->isMetadataPresent('creationDate'))
      unset($this->meta['creationDate']);
  }


  public function getConfirmationHash() {
    return $this->meta['confirmationHash'];
  }


  public function issetConfirmationHash() {
    return isset($this->meta['confirmationHash']);
  }


  public function setConfirmationHash($value) {
    $this->meta['confirmationHash'] = $value;
  }

  //! @endcond

}
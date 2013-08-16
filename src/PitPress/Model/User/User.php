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


  //! @brief Last time the user has logged in.
  public function getLastVisit($value) {
    $this->meta['lastVisit'] = $value;
  }


  //! @brief Returns the user's reputation.
  //! @return integer
  public function getReputation() {
    $opts = new ViewQueryOpts();
    $opts->setKey([$this->id]);

    $result = $this->couch->queryView("reputation", "perUser", NULL, $opts)->getBodyAsArray();

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


  //! @name Authentication Methods
  // @{

  //! @brief Authenticate the user.
  public function authenticate() {
    $this->meta['authenticated'] = "true";
  }


  //! @brief Returns TRUE if the user has been authenticated.
  public function isAuthenticated() {
    return isset($this->meta['authenticated']);
  }


  public function getConfirmationHash($value) {
    $this->meta['confirmationHash'] = $value;
  }

  //@}


  //! @name Ban Management Methods
  // @{

  //! @brief Bans the user.
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

  public function getAge() {

  }


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
    $this->meta['email'] = $value;
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
  

  public function getIPAddress($value) {
    $this->meta['ipAddress'] = $value;
  }


  public function issetIPAddress() {
    return isset($this->meta['ipAddress']);
  }


  public function setIPAddress($value) {
    $this->meta['ipAddress'] = $value;
  }

  //! @endcond

}
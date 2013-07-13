<?php

//! @file User.php
//! @brief This file contains the User class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\User;


use PitPress\Model\Item;
use PitPress\Model\Helper;


//! @brief This class is used to represent a registered user.
//! @nosubgrouping
class User extends Item {
  use Helper\ViewTrait;


  //! @brief Last time the user has logged in.
  public function getLastVisit($value) {
    $this->meta['lastVisit'] = $value;
  }


  public function getIPAddress($value) {
    $this->meta['ipAddress'] = $value;
  }


  public function getConfirmationHash($value) {
    $this->meta['confirmationHash'] = $value;
  }


  //! @brief Authenticate the user.
  public function authenticate() {
    $this->meta['authenticated'] = "true";
  }


  //! @brief Returns TRUE if the user has been authenticated.
  public function isAuthenticated() {
    return isset($this->meta['authenticated']);
  }


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


  //! @brief Returns <i>true</i> if the user has been banned.
  public function isBanned() {
    return isset($this->meta['banned']);
  }


  //! @brief Returns the user's age.
  public function getAge() {

  }


  public function getFirstName($value) {
    $this->meta['firstName'] = $value;
  }


  public function issetFirstName() {
    return isset($this->meta['firstName']);
  }


  public function setFirstName() {
    return $this->meta['firstName'];
  }


  public function unsetFirstName() {
    if ($this->isMetadataPresent('firstName'))
      unset($this->meta['firstName']);
  }


  public function getLastName($value) {
    $this->meta['lastName'] = $value;
  }


  public function issetLastName() {
    return isset($this->meta['lastName']);
  }


  public function setLastName() {
    return $this->meta['lastName'];
  }


  public function getDisplayName($value) {
    $this->meta['displayName'] = $value;
  }


  public function issetDisplayName() {
    return isset($this->meta['displayName']);
  }


  public function setDisplayName() {
    return $this->meta['displayName'];
  }


  public function getEmail($value) {
    $this->meta['email'] = $value;
  }


  public function issetEmail() {
    return isset($this->meta['email']);
  }


  public function setEmail() {
    return $this->meta['email'];
  }


  public function getPassword($value) {
    $this->meta['password'] = $value;
  }


  public function issetPassword() {
    return isset($this->meta['password']);
  }


  public function setPassword() {
    return $this->meta['password'];
  }


  public function getSex($value) {
    $this->meta['sex'] = $value;
  }


  public function issetSex() {
    return isset($this->meta['sex']);
  }


  public function setSex() {
    return $this->meta['sex'];
  }


  public function getBirthday($value) {
    $this->meta['birthday'] = $value;
  }


  public function issetBirthday() {
    return isset($this->meta['birthday']);
  }


  public function setBirthday() {
    return $this->meta['birthday'];
  }

}
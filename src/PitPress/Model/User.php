<?php

//! @file User.php
//! @brief This file contains the User class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model;


use ElephantOnCouch\Doc\Doc;


//! @brief This class is used to represent a registered user.
//! @nosubgrouping
class User extends AbstractItem {


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


  public function unsetLastName() {
    if ($this->isMetadataPresent('lastName'))
      unset($this->meta['lastName']);
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


  public function unsetDisplayName() {
    if ($this->isMetadataPresent('displayName'))
      unset($this->meta['displayName']);
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


  public function unsetEmail() {
    if ($this->isMetadataPresent('email'))
      unset($this->meta['email']);
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


  public function unsetPassword() {
    if ($this->isMetadataPresent('password'))
      unset($this->meta['password']);
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


  public function unsetSex() {
    if ($this->isMetadataPresent('sex'))
      unset($this->meta['sex']);
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


  public function unsetBirthday() {
    if ($this->isMetadataPresent('birthday'))
      unset($this->meta['birthday']);
  }


  public function getCreationDate($value) {
    $this->meta['creationDate'] = $value;
  }


  public function issetCreationDate() {
    return isset($this->meta['creationDate']);
  }


  public function setCreationDate() {
    return $this->meta['creationDate'];
  }


  public function unsetCreationDate() {
    if ($this->isMetadataPresent('creationDate'))
      unset($this->meta['creationDate']);
  }


  public function getLastUpdate($value) {
    $this->meta['lastUpdate'] = $value;
  }


  public function issetLastUpdate() {
    return isset($this->meta['lastUpdate']);
  }


  public function setLastUpdate() {
    return $this->meta['lastUpdate'];
  }


  public function unsetLastUpdate() {
    if ($this->isMetadataPresent('lastUpdate'))
      unset($this->meta['lastUpdate']);
  }


  public function getLastVisit($value) {
    $this->meta['lastVisit'] = $value;
  }


  public function issetLastVisit() {
    return isset($this->meta['lastVisit']);
  }


  public function setLastVisit() {
    return $this->meta['lastVisit'];
  }


  public function unsetLastVisit() {
    if ($this->isMetadataPresent('lastVisit'))
      unset($this->meta['lastVisit']);
  }


  public function getIPAddress($value) {
    $this->meta['ipAddress'] = $value;
  }


  public function issetIPAddress() {
    return isset($this->meta['ipAddress']);
  }


  public function setIPAddress() {
    return $this->meta['ipAddress'];
  }


  public function unsetIPAddress() {
    if ($this->isMetadataPresent('ipAddress'))
      unset($this->meta['ipAddress']);
  }


  public function getConfirmationHash($value) {
    $this->meta['confirmationHash'] = $value;
  }


  public function issetConfirmationHash() {
    return isset($this->meta['confirmationHash']);
  }


  public function setConfirmationHash() {
    return $this->meta['confirmationHash'];
  }


  public function unsetConfirmationHash() {
    if ($this->isMetadataPresent('confirmationHash'))
      unset($this->meta['confirmationHash']);
  }


  //! @brief Returns the user's age.
  public function getAge() {

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
  public function ban() {
    $this->meta['banned'] = "true";
  }


  //! @brief Unban the user.
  public function unban() {
    if ($this->isMetadataPresent('banned'))
      unset($this->meta['banned']);
  }


  //! @brief Returns TRUE if the user has been banned.
  public function isBanned() {
    return isset($this->meta['banned']);
  }

}
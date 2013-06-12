<?php

//! @file User.php
//! @brief This file contains the User class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model;


use ElephantOnCouch\Doc\Doc;


//! @brief This class is used to represent a registered user.
//! @nosubgrouping
class User extends Doc {

  //! @name User's Attributes
  //! @brief Those are standard user's attributes.
  //@{
  const FIRST_NAME = "firstName"; //!< User's name.
  const LAST_NAME = "lastName"; //!< User's surname.
  const DISPLAY_NAME = "displayName"; //!< The name to be displayed on the site.

  const EMAIL = "email"; //!< User's e-mail.
  const PASSWORD = "password"; //!< Password chosen by the user.

  const SEX = "sex"; //!< Sex.
  const BIRTHDAY = "birthday"; //!< Date of birth.

  const CREATION_DATE = "creationDate"; //!< Registration date.
  const LAST_UPDATE = "lastUpdate"; //!< Last time the user has updated his profile.
  const LAST_VISIT = "lastVisit"; //!< Last time the user has logged in.

  // We don't use these constants, because every registered users must agree to the Terms of Services, Privacy Policy and
  // Cookie policy. Here just to remember them.
  //const TERMS_OF_SERVICE = "termsOfService";
  //const PRIVACY_POLICY = "privacyPolicy";
  //const COOKIE_POLICY = "cookiePolicy";

  const IP_ADDRESS = "idAddress"; //!< Last known user's IP address.
  const CONFIRMATION_HASH = "confirmationHash"; //!< Confirmation hash.
  const AUTHENTICATED = "authenticated"; //!< The user has been authenticated.
  const BANNED = "banned"; //!< The uses has been banned.
  //@}


  public function getFirstName($value) {
    $this->meta[self::FIRST_NAME] = $value;
  }


  public function issetFirstName() {
    return isset($this->meta[self::FIRST_NAME]);
  }


  public function setFirstName() {
    return $this->meta[self::FIRST_NAME];
  }


  public function unsetFirstName() {
    if ($this->isMetadataPresent(self::FIRST_NAME))
      unset($this->meta[self::FIRST_NAME]);
  }


  public function getLastName($value) {
    $this->meta[self::LAST_NAME] = $value;
  }


  public function issetLastName() {
    return isset($this->meta[self::LAST_NAME]);
  }


  public function setLastName() {
    return $this->meta[self::LAST_NAME];
  }


  public function unsetLastName() {
    if ($this->isMetadataPresent(self::LAST_NAME))
      unset($this->meta[self::LAST_NAME]);
  }


  public function getDisplayName($value) {
    $this->meta[self::DISPLAY_NAME] = $value;
  }


  public function issetDisplayName() {
    return isset($this->meta[self::DISPLAY_NAME]);
  }


  public function setDisplayName() {
    return $this->meta[self::DISPLAY_NAME];
  }


  public function unsetDisplayName() {
    if ($this->isMetadataPresent(self::DISPLAY_NAME))
      unset($this->meta[self::DISPLAY_NAME]);
  }


  public function getEmail($value) {
    $this->meta[self::EMAIL] = $value;
  }


  public function issetEmail() {
    return isset($this->meta[self::EMAIL]);
  }


  public function setEmail() {
    return $this->meta[self::EMAIL];
  }


  public function unsetEmail() {
    if ($this->isMetadataPresent(self::EMAIL))
      unset($this->meta[self::EMAIL]);
  }


  public function getPassword($value) {
    $this->meta[self::PASSWORD] = $value;
  }


  public function issetPassword() {
    return isset($this->meta[self::PASSWORD]);
  }


  public function setPassword() {
    return $this->meta[self::PASSWORD];
  }


  public function unsetPassword() {
    if ($this->isMetadataPresent(self::PASSWORD))
      unset($this->meta[self::PASSWORD]);
  }


  public function getSex($value) {
    $this->meta[self::SEX] = $value;
  }


  public function issetSex() {
    return isset($this->meta[self::SEX]);
  }


  public function setSex() {
    return $this->meta[self::SEX];
  }


  public function unsetSex() {
    if ($this->isMetadataPresent(self::SEX))
      unset($this->meta[self::SEX]);
  }


  public function getBirthday($value) {
    $this->meta[self::BIRTHDAY] = $value;
  }


  public function issetBirthday() {
    return isset($this->meta[self::BIRTHDAY]);
  }


  public function setBirthday() {
    return $this->meta[self::BIRTHDAY];
  }


  public function unsetBirthday() {
    if ($this->isMetadataPresent(self::BIRTHDAY))
      unset($this->meta[self::BIRTHDAY]);
  }


  public function getCreationDate($value) {
    $this->meta[self::CREATION_DATE] = $value;
  }


  public function issetCreationDate() {
    return isset($this->meta[self::CREATION_DATE]);
  }


  public function setCreationDate() {
    return $this->meta[self::CREATION_DATE];
  }


  public function unsetCreationDate() {
    if ($this->isMetadataPresent(self::CREATION_DATE))
      unset($this->meta[self::CREATION_DATE]);
  }


  public function getLastUpdate($value) {
    $this->meta[self::LAST_UPDATE] = $value;
  }


  public function issetLastUpdate() {
    return isset($this->meta[self::LAST_UPDATE]);
  }


  public function setLastUpdate() {
    return $this->meta[self::LAST_UPDATE];
  }


  public function unsetLastUpdate() {
    if ($this->isMetadataPresent(self::LAST_UPDATE))
      unset($this->meta[self::LAST_UPDATE]);
  }


  public function getLastVisit($value) {
    $this->meta[self::LAST_VISIT] = $value;
  }


  public function issetLastVisit() {
    return isset($this->meta[self::LAST_VISIT]);
  }


  public function setLastVisit() {
    return $this->meta[self::LAST_VISIT];
  }


  public function unsetLastVisit() {
    if ($this->isMetadataPresent(self::LAST_VISIT))
      unset($this->meta[self::LAST_VISIT]);
  }


  public function getIPAddress($value) {
    $this->meta[self::IP_ADDRESS] = $value;
  }


  public function issetIPAddress() {
    return isset($this->meta[self::IP_ADDRESS]);
  }


  public function setIPAddress() {
    return $this->meta[self::IP_ADDRESS];
  }


  public function unsetIPAddress() {
    if ($this->isMetadataPresent(self::IP_ADDRESS))
      unset($this->meta[self::IP_ADDRESS]);
  }


  public function getConfirmationHash($value) {
    $this->meta[self::CONFIRMATION_HASH] = $value;
  }


  public function issetConfirmationHash() {
    return isset($this->meta[self::CONFIRMATION_HASH]);
  }


  public function setConfirmationHash() {
    return $this->meta[self::CONFIRMATION_HASH];
  }


  public function unsetConfirmationHash() {
    if ($this->isMetadataPresent(self::CONFIRMATION_HASH))
      unset($this->meta[self::CONFIRMATION_HASH]);
  }


  //! @brief Returns the user's age.
  public function getAge() {

  }


  //! @brief Authenticate the user.
  public function authenticate() {
    $this->meta[self::AUTHENTICATED] = "true";
  }


  //! @brief Returns TRUE if the user has been authenticated.
  public function isAuthenticated() {
    return isset($this->meta[self::AUTHENTICATED]);
  }


  //! @brief Bans the user.
  public function ban() {
    $this->meta[self::BANNED] = "true";
  }


  //! @brief Unban the user.
  public function unban() {
    if ($this->isMetadataPresent(self::BANNED))
      unset($this->meta[self::BANNED]);
  }


  //! @brief Returns TRUE if the user has been banned.
  public function isBanned() {
    return isset($this->meta[self::BANNED]);
  }

}
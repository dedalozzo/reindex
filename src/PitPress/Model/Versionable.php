<?php

/**
 * @file Versionable.php
 * @brief This file contains the Versionable class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model;


use ElephantOnCouch\Opt\ViewQueryOpts;
use ElephantOnCouch\Generator\UUID;

use PitPress\Helper\Time;


/**
 * @brief A generic content created by a user.
 * @nosubgrouping
 */
abstract class Versionable extends Storable {

  const NO_USER_LOGGED_IN = -1; //!< No user logged in. The user is a guest.


  /** @name Control Versioning Methods */
  //!@{

  /**
   * @brief Creates an instance of the class, modifying opportunely the ID, appending a version number.
   * @details Versioned items, in fact, share the same ID, but a version number is added to differentiate them.
   * @param[in] string $id When provided use it appending the version number, else a new ID is generated.
   * @return object
   */
  public static function create($id = NULL) {
    $instance = new static();

    if (is_null($id))
      $instance->meta['_id'] = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING).self::SEPARATOR.(string)time();
    else
      $instance->meta['_id'] = (string)$id.self::SEPARATOR.(string)time();

    return $instance;
  }


  /**
   * @brief Returns `true` if this document revision is the current one, `false` otherwise.
   * @return bool
   */
  public function isCurrent() {
    return ($this->isMetadataPresent('current')) ? TRUE : FALSE;
  }


  /**
   * @brief Approves this document revision, making of it the current version.
   */
  public function approve() {
    $this->meta['current'] = TRUE;
  }


  /**
   * @brief Reverts to the specified version.
   * @param[in] Reverts to the specified version. If a version is not specified it takes the previous one.
   */
  public function revert($version = NULL) {
    // todo
  }


  /**
   * @brief Gets information about all the previous versions.
   * @return array
   */
  public function getPastVersionsInfo() {
    // todo
  }

  //!@}


  /**
   * @copydoc Storable.save
   */
  public function save() {
    // Put your code here.
    parent::save();
  }


  /**
   * @brief Returns a measure of the time passed since the publishing date. In case is passed more than a day, returns
   * a human readable date.
   * @return string
   */
  public function whenHasBeenPublished() {
    return Time::when($this->publishingDate);
  }


  /**
   * @brief Returns the author's username.
   */
  public function getUsername() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setKey($this->userId);
    return $this->couch->queryView("users", "allNames", NULL, $opts)[0]['value'][0];
  }


  /**
   * @brief Builds the gravatar uri.
   * @return string
   */
  public function getGravatar() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setKey($this->userId);
    $email = $this->couch->queryView("users", "allNames", NULL, $opts)[0]['value'][1];
    return 'http://gravatar.com/avatar/'.md5(strtolower($email)).'?d=identicon';
  }


 //! @cond HIDDEN_SYMBOLS

  public function getVersionNumber() {
    return substr($this->meta['_id'], stripos($this->meta['_id'], self::SEPARATOR) + strlen(self::SEPARATOR));
  }


  public function issetVersionNumber() {
    if (stripos($this->meta['_id'], self::SEPARATOR) === FALSE)
      return FALSE;
    else
      return TRUE;
  }


  public function setVersionNumber($value) {
    $this->meta['_id'] = $this->getUnversionId().self::SEPARATOR.(string)$value;
  }


  public function unsetVersionNumber() {
    $this->meta['_id'] = $this->getUnversionId();
  }


  public function getPreviousVersionNumber() {
    if ($this->isMetadataPresent('previousVersionNumber'))
      return $this->meta['previousVersionNumber'];
    else
      return NULL;
  }


  public function getUserId() {
    return $this->meta["userId"];
  }


  public function issetUserId() {
    return isset($this->meta['userId']);
  }


  public function setUserId($value) {
    $this->meta["userId"] = $value;
  }


  public function unsetUserId() {
    if ($this->isMetadataPresent('userId'))
      unset($this->meta['userId']);
  }


  public function getPublishingDate() {
    return $this->meta['publishingDate'];
  }


  public function issetPublishingDate() {
    return isset($this->meta['publishingDate']);
  }


  public function setPublishingDate($value) {
    $this->meta['publishingDate'] = $value;
  }


  public function unsetPublishingDate() {
    if ($this->isMetadataPresent('publishingDate'))
      unset($this->meta['publishingDate']);
  }

  //! @endcond

}
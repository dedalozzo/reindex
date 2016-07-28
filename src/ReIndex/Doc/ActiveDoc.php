<?php

/**
 * @file ActiveDoc.php
 * @brief This file contains the ActiveDoc class.
 * @details
 * @author Filippo F. Fadda
 */


//! CouchDB's documents
namespace ReIndex\Doc;


use EoC\Couch;
use EoC\Doc\Doc;
use EoC\Generator\UUID;

use Monolog\Logger;

use Phalcon\Di;

use ReIndex\Helper;


/**
 * @brief This class is used to represent a storable object and implements the Active Record patten.
 * @nosubgrouping
 *
 * @cond HIDDEN_SYMBOLS
 *
 * @property string $createdAt
 * @property string $modifiedAt
 *
 * @property string $creatorId
 * @property string $editorId
 *
 * @endcond
 */
abstract class ActiveDoc extends Doc {

  /**
   * @var Di $di
   */
  protected $di;

  /**
   * @var Couch $couch
   */
  protected $couch;

  /**
   * @var \Redis $redis
   */
  protected $redis;

  /**
   * @var Logger $log
   */
  protected $log;

  /**
   * @var Member $user
   */
  protected $user;


  /**
   * @brief Constructor.
   */
  public function __construct() {
    $this->di = Di::getDefault();
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];
    $this->log = $this->di['log'];

    $this->user = $this->di['guardian']->getUser();
  }


  /**
   * @brief Creates an instance of the class, using the provided ID if any, or generating a new one.
   * @param[in] string $id (optional) An optional ID.
   * @retval object
   */
  public static function create($id = NULL) {
    $instance = new static();

    if (is_null($id))
      $instance->setId(UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING));
    else
      $instance->setId($id);

    return $instance;
  }


  /**
   * @brief Tries to get the object from the database identified by the provided ID.
   * @param[in] string $id An object ID.
   * @retval object
   */
  public static function find($id) {
    $di = Di::getDefault();
    $couch = $di['couchdb'];

    return $couch->getDoc(Couch::STD_DOC_PATH, $id);
  }


  /**
   * @brief Saves the item to the database.
   */
  public function save() {
    // Creator ID has not been provided.
    if (!isset($this->creatorId))
      $this->creatorId = $this->user->id;

    // Sets the editing timestamp.
    $this->modifiedAt = time();

    // Creation timestamp has not been provided.
    if (!isset($this->createdAt))
      $this->createdAt = $this->modifiedAt;

    $this->couch->saveDoc($this);
  }


  /**
   * @brief Returns `true` if the provided user id matches the current one, `false` otherwise.
   * @details This method is useful to check the ownership of a post, for example.
   * @param[in] string $id The id to match.
   * @retval bool
   */
  public function match($id) {
    return ($this->id === $id) ? TRUE : FALSE;
  }


  /**
   * @brief Returns a measure of the time passed since the creation time. In case is passed more than a day, returns
   * a human readable date.
   * @retval string
   */
  public function whenHasBeenCreated() {
    return Helper\Time::when($this->createdAt);
  }


  /**
   * @brief Returns a measure of the time passed since the last modification. In case is passed more than a day, returns
   * a human readable date.
   * @retval string
   */
  public function whenHasBeenModified() {
    return Helper\Time::when($this->modifiedAt);
  }


  //! @cond HIDDEN_SYMBOLS

  public function getCreatedAt() {
    return $this->meta['createdAt'];
  }


  public function issetCreatedAt() {
    return isset($this->meta['createdAt']);
  }


  public function setCreatedAt($value) {
    $this->meta['createdAt'] = $value;
  }


  public function unsetCreatedAt() {
    if ($this->isMetadataPresent('createdAt'))
      unset($this->meta['createdAt']);
  }


  public function getModifiedAt() {
    return $this->meta['modifiedAt'];
  }


  public function issetModifiedAt() {
    return isset($this->meta['modifiedAt']);
  }


  public function setModifiedAt($value) {
    $this->meta['modifiedAt'] = $value;
  }


  public function unsetModifiedAt() {
    if ($this->isMetadataPresent('modifiedAt'))
      unset($this->meta['modifiedAt']);
  }


  public function getCreatorId() {
    return $this->meta["creatorId"];
  }


  public function issetCreatorId() {
    return isset($this->meta['creatorId']);
  }


  public function setCreatorId($value) {
    $this->meta["creatorId"] = $value;
  }


  public function unsetCreatorId() {
    if ($this->isMetadataPresent('creatorId'))
      unset($this->meta['creatorId']);
  }


  public function getEditorId() {
    return $this->meta["editorId"];
  }


  public function issetEditorId() {
    return isset($this->meta['editorId']);
  }


  public function setEditorId($value) {
    $this->meta["editorId"] = $value;
  }


  public function unsetEditorId() {
    if ($this->isMetadataPresent('editorId'))
      unset($this->meta['editorId']);
  }

  //! @endcond

}
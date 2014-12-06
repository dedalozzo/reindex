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

use PitPress\Helper;
use PitPress\Enum\DocStatus;


/**
 * @brief A generic content created by a user.
 * @nosubgrouping
 */
abstract class Versionable extends Storable {


  /**
   * @brief Constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->status = DocStatus::CREATED;
    $this->meta['versionable'] = TRUE;
  }


  /** @name Control Versioning Methods */
  //!@{

  /**
   * @brief Creates an instance of the class, modifying opportunely the ID, appending a version number.
   * @details Versioned items, in fact, share the same ID, but a version number is added to differentiate them.
   * @param[in] string $id When provided, appends the version number, else a new ID is generated.
   * @return object
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
   * @brief Moves the document to the trash.
   */
  public function moveToTrash() {
    $this->meta['prevStatus'] = $this->meta['status'];
    $this->meta['status'] = DocStatus::DELETED;
  }


  /**
   * @brief Restores the document to its previous status, removing it from trash.
   */
  public function restore() {
    // In case the document has been deleted, restore it to its previous status.
    if ($this->meta['status'] == DocStatus::DELETED) {
      $this->meta['status'] = $this->meta['prevStatus'];
      unset($this->meta['prevStatus']);
    }
  }


  /**
   * @brief Submits the document for peer review.
   */
  public function submit() {
    $this->meta['status'] = DocStatus::SUBMITTED;
  }


  /**
   * @brief Approves the document revision, making of it the current version.
   */
  public function approve() {
    $this->meta['status'] = DocStatus::CURRENT;
  }


  /**
   * @brief Rejects this document revision.
   * @details The post will be automatically deleted in 10 days.
   * @param[in] The reason why the revision has been rejected.
   */
  public function reject($reason) {
    // todo: send a notification to the user
    $this->meta['status'] = DocStatus::REJECTED;
    $this->meta['rejectReason'] = $reason;
  }


  /**
   * @brief Asks the author to revise the item, because it's not ready for publishing.
   * @param[in] The reason why the document has been returned for revision.
   */
  public function returnForRevision($reason) {
    // todo: send a notification to the user
    $this->meta['status'] = DocStatus::RETURNED;
    $this->meta['rejectReason'] = $reason;
  }


  /**
   * @brief Reverts to the specified version.
   * @param[in] Reverts to the specified version. If a version is not specified it takes the previous one.
   */
  public function revert($versionNumber = NULL) {
    // todo
    $this->meta['status'] = DocStatus::APPROVED;
  }


  /**
   * @brief Returns `true` if this document revision is the current one, `false` otherwise.
   * @return bool
   */
  public function isCurrent() {
    return ($this->isMetadataPresent('status') && $this->getStatus() == DocStatus::CURRENT) ? TRUE : FALSE;
  }


  /**
   * @brief Returns `true` if this document has been put into the trash, `false` otherwise.
   * @return bool
   */
  public function isGarbage() {
    return ($this->isMetadataPresent('status') && $this->getStatus() == DocStatus::DELETED) ? TRUE : FALSE;
  }


  /**
   * @brief Returns `true` if this document has been submitted for peer review, `false` otherwise.
   * @return bool
   */
  public function hasBeenSubmittedForPeerReview() {
    return ($this->isMetadataPresent('status') && $this->getStatus() == DocStatus::SUBMITTED) ? TRUE : FALSE;
  }


  /**
   * @brief Returns `true` if this document revision has been approved, `false` otherwise.
   * @return bool
   */
  public function hasBeenApproved() {
    return ($this->isMetadataPresent('status') && $this->getStatus() == DocStatus::APPROVED) ? TRUE : FALSE;
  }


  /**
   * @brief Returns `true` if this document revision has been rejected, `false` otherwise.
   * @return bool
   */
  public function hasBeenRejected() {
    return ($this->isMetadataPresent('status') && $this->getStatus() == DocStatus::REJECTED) ? TRUE : FALSE;
  }


  /**
   * @brief Returns `true` if this document has been returned for revision, `false` otherwise.
   * @return bool
   */
  public function hasBeenReturnedForRevision() {
    return ($this->isMetadataPresent('status') && $this->getStatus() == DocStatus::RETURNED) ? TRUE : FALSE;
  }

  //@}



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

  public function setId($value) {
    $pos = stripos($value, Helper\Text::SEPARATOR);
    $this->meta['unversionId'] = Helper\Text::unversion($value);
    $this->meta['versionNumber'] = ($pos) ? substr($value, $pos + strlen(Helper\Text::SEPARATOR)) : (string)time();
    $this->meta['_id'] = $this->meta['unversionId'] . Helper\Text::SEPARATOR . $this->meta['versionNumber'];
  }


  public function getUnversionId() {
    return $this->meta["unversionId"];
  }


  public function issetUnversionId() {
    return isset($this->meta["unversionId"]);
  }


  public function getVersionNumber() {
    return $this->meta["versionNumber"];
  }


  public function issetVersionNumber() {
    return isset($this->meta['versionNumber']);
  }


  public function getPreviousVersionNumber() {
    return $this->meta["previousVersionNumber"];
  }


  public function issetPreviousVersionNumber() {
    return isset($this->meta['previousVersionNumber']);
  }


  public function getStatus() {
    return $this->meta["status"];
  }


  public function issetStatus() {
    return isset($this->meta['status']);
  }


  public function setStatus($value) {
    $this->meta["status"] = $value;
  }


  public function unsetStatus() {
    if ($this->isMetadataPresent('status'))
      unset($this->meta['status']);
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


  public function getEditSummary() {
    return $this->meta["editSummary"];
  }


  public function issetEditSummary() {
    return isset($this->meta['editSummary']);
  }


  public function setEditSummary($value) {
    $this->meta["editSummary"] = $value;
  }


  public function unsetEditSummary() {
    if ($this->isMetadataPresent('editSummary'))
      unset($this->meta['editSummary']);
  }

  //! @endcond

}
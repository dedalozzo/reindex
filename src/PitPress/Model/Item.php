<?php

//! @file Item.php
//! @brief This file contains the Item class.
//! @details
//! @author Filippo F. Fadda


//! @brief PitPress models namespace.
namespace PitPress\Model;


use ElephantOnCouch\Doc\Doc;


//! @brief
//! @nosubgrouping
class Item extends Doc {

  //! @name Item's Attributes
  //! @brief Those are standard item's attributes.
  //@{
  const NAME = "name"; //!< Document's name.
  //@}

  //! @name States
  //@{
  const DRAFT_STATE = "draft"; //!< The item is a draft.
  const REJECTED_STATE = "rejected"; //!< The item has been rejected.
  const ASKED_FOR_REVISION_STATE = "asked_for_revision"; //!< The item has been asked for revision.
  const SUBMITTED_FOR_PUBLISHING_STATE = "submitted_for_publishing"; //!< The item has been submitted for publishing.
  const PUBLISHED_STATE = "published"; //!< The item has been published.
  const TRASHED_STATE = "trashed"; //!< The item has been trashed.
  //@}


  public function __construct() {

  }


  public function getState() {

  }


  // Creation timestamp.
  public function setCreationDate($value) {
    $this->meta["creationDate"] = $value;
  }


  public function getCreationDate() {
    return $this->meta["creationDate"];
  }


  // Publishing timestamp.
  public function setPublishingDate($value) {
    $this->meta["publishingDate"] = $value;
  }


  public function getPublishingDate() {
    return $this->meta["publishingDate"];
  }


  public function isTrashed() {
    return $this->meta["trashed"];
  }


  public function isPinned() {
    return $this->meta["pinned"];
  }


  public function isClosed() {
    return $this->meta["closed"];
  }


  public function areCommentAllowed() {
    return $this->meta["areCommentAllowed"];
  }


  // Tell if the item needs to be approved to appear on the Journal.
  public function waitingForApproval() {
    return $this->meta["waitingForApproval"];
  }


  public function setOwnerId($value) {
    $this->meta["ownerId"] = $value;
  }


  public function getOwnerId() {
    return $this->meta["ownerId"];
  }


  public function getName() {
    return $this->meta[self::NAME];
  }

  public function issetName() {
    return isset($this->meta[self::NAME]);
  }


  public function setName($value) {
    $this->meta[self::NAME] = $value;
  }

  public function unsetName() {
    if ($this->isMetadataPresent(self::NAME))
      unset($this->meta[self::NAME]);
  }

  // A general text field where store text.
  public function setBody($value) {
    $this->meta["body"] = $value;
  }


  public function getBody() {
    return $this->meta["body"];
  }


  public function follow() {

  }


  public function unfollow() {

  }


  public function moveToTrash() {

  }


  public function putBack() {

  }


  public function getLastUpdateInfo() {

  }


  public function getReplaysCount() {

  }


  public function getDisplaysCount() {

  }

  public function incDisplays() {
  }


  public function getPosts() {

  }


  public function getTags() {

  }


  public function resetTags() {

  }


  public function addTag() {

  }


  public function removetag() {

  }


  public function addMultipleTagAtOnce() {

  }


  public function star() {

  }


  public function unstar() {

  }


  public function pin() {

  }


  public function unpin() {

  }


  public function close() {

  }


  public function reopen() {

  }


  public function hide() {

  }


  protected function needForApproval() {

  }


  public function markAsDraft() {

  }


  public function submitForPublishing() {

  }


  public function askForRevision($reason) {

  }


  public function acceptRevision() {

  }


  public function rejectRevision($reason) {

  }


  public function publish() {

  }


  public function getPermalink() {

  }


  public function flag() {

  }

}
<?php

/**
 * @file Versionable.php
 * @brief This file contains the Versionable class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Model;


use EoC\Opt\ViewQueryOpts;

use ReIndex\Helper;
use ReIndex\Exception;
use ReIndex\Enum\DocStatus;


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
    $this->meta["status"] = DocStatus::CREATED;
    $this->meta['versionable'] = TRUE;
  }


  /** @name Status Checking Methods */
  //!@{

  /**
   * @brief Returns `true` in case the provided status matches the current version status.
   * @param[in] string $status The status to check.
   * @retval bool
   */
  protected function checkStatus($status) {
    return ($this->meta["status"] === $status) ? TRUE : FALSE;
  }


  /**
   * @brief Returns the version status.
   * @retval string
   */
  public function getStatus() {
    return $this->meta["status"];
  }


  /**
   * @brief Returns `true` if this document has just been created, `false` otherwise.
   * @retval bool
   */
  public function isCreated() {
    return $this->checkStatus(DocStatus::CREATED);
  }


  /**
   * @brief Returns `true` if this document is only a draft, `false` otherwise.
   * @retval bool
   */
  public function isDraft() {
    return $this->checkStatus(DocStatus::DRAFT);
  }


  /**
   * @brief Returns `true` if this document revision is the current one, `false` otherwise.
   * @retval bool
   */
  public function isCurrent() {
    return $this->checkStatus(DocStatus::CURRENT);
  }


  /**
   * @brief Returns `true` if this document has been put into the trash, `false` otherwise.
   * @retval bool
   */
  public function isMovedToTrash() {
    return $this->checkStatus(DocStatus::DELETED);
  }


  /**
   * @brief Returns `true` if this document has been submitted for peer review, `false` otherwise.
   * @retval bool
   */
  public function isSubmittedForPeerReview() {
    return $this->checkStatus(DocStatus::SUBMITTED);
  }


  /**
   * @brief Returns `true` if this document revision has been approved, `false` otherwise.
   * @retval bool
   */
  public function isApproved() {
    return $this->checkStatus(DocStatus::APPROVED);
  }


  /**
   * @brief Returns `true` if this document revision has been rejected, `false` otherwise.
   * @retval bool
   */
  public function isRejected() {
    return $this->checkStatus(DocStatus::REJECTED);
  }


  /**
   * @brief Returns `true` if this document has been returned for revision, `false` otherwise.
   * @retval bool
   */
  public function isReturnedForRevision() {
    return $this->checkStatus(DocStatus::RETURNED);
  }

  //@}


  /** @name Access Control Methods */
  //!@{

  /**
   * @brief Returns `true` if the post can be viewed by the current user, `false` otherwise.
   * @retval bool
   */
  public function canBeViewed() {
    if ($this->isCurrent()) return TRUE;

    elseif ($this->user->match($this->creatorId)) return TRUE;

    elseif ($this->user->isEditor() && $this->approved()) return TRUE;

    elseif ($this->user->isModerator() &&
      ($this->isSubmittedForPeerReview() or
        $this->isReturnedForRevision() or
        $this->isRejected() or
        $this->isMovedToTrash()))
      return TRUE;
    else
      return FALSE;
  }


  /**
   * @brief Returns `true` if the document can be edited, `false` otherwise.
   * @retval bool
   */
  public function canBeEdited() {
    if (($this->user->isAdmin() or $this->user->isEditor() or $this->user->match($this->creatorId)) &&
      ($this->isCurrent() or $this->isDraft()))
      return TRUE;
    else
      return FALSE;
  }


  /**
   * @brief Returns `true` if the document can be submitted for peer review, `false` otherwise.
   * @retval bool
   */
  public function canBeSubmitted() {
    if ($this->isSubmittedForPeerReview()) return FALSE;

    if ($this->user->match($this->creatorId) && ($this->isCreated() or $this->isDraft()))
      return TRUE;
    else
      return FALSE;
  }


  /**
   * @brief Returns `true` if the document can be approved, `false` otherwise.
   * @retval bool
   */
  public function canBeApproved() {
    if ($this->user->isModerator() && ($this->isCreated() or $this->isDraft() or $this->isSubmittedForPeerReview()))
      return TRUE;
    else
      return FALSE;
  }


  /**
   * @brief Returns `true` if the member can ask the original author to revise the document, `false` otherwise.
   * @retval bool
   */
  public function canBeReturnedForRevision() {
    if ($this->isReturnedForRevision()) return FALSE;

    if (($this->user->isModerator() && $this->isSubmittedForPeerReview()) or
      ($this->user->isAdmin() && $this->isCurrent()))
      return TRUE;
    else
      return FALSE;
  }


  /**
   * @brief Returns `true` if the document revision can be rejected, `false` otherwise.
   * @retval bool
   */
  public function canBeRejected() {
    if ($this->user->isModerator() && $this->isSubmittedForPeerReview())
      return TRUE;
    else
      return FALSE;
  }


  /**
   * @brief Returns `true` if the document can be reverted to another version, `false` otherwise.
   * @retval bool
   */
  public function canBeReverted() {
    if ($this->user->isModerator())
      return TRUE;
    else
      return FALSE;
  }


  /**
   * @brief Returns `true` if the document can be moved to trash, `false` otherwise.
   * @retval bool
   */
  public function canBeMovedToTrash() {
    if ($this->isMovedToTrash()) return FALSE;

    if (($this->user->isModerator() && $this->isCurrent()) or ($this->user->match($this->creatorId) && $this->isDraft()))
      return TRUE;
    else
      return FALSE;
  }


  /**
   * @brief Returns `true` if the post can be moved to trash, `false` otherwise.
   * @retval bool
   */
  public function canBeRestored() {
    if ($this->isMovedToTrash() and
      ($this->user->isModerator() && ($this->dustmanId == $this->user->id)) or
      $this->user->isAdmin())
      return TRUE;
    else
      return FALSE;
  }

  //@}


  /** @name Control Versioning Methods */
  //!@{

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
   * @brief Asks the author to revise the document, because it's not ready for publishing.
   * @param[in] $reason The reason why the document has been returned for revision.
   */
  public function returnForRevision($reason) {
    $this->meta['status'] = DocStatus::RETURNED;
    $this->meta['rejectReason'] = $reason;
    $this->meta['moderatorId'] = $this->user->id;
    // todo: send a notification to the user
  }


  /**
   * @brief Rejects this document revision.
   * @details The post will be automatically deleted in 10 days.
   * @param[in] $reason The reason why the revision has been rejected.
   */
  public function reject($reason) {
    $this->meta['status'] = DocStatus::REJECTED;
    $this->meta['rejectReason'] = $reason;
    $this->meta['moderatorId'] = $this->user->id;
    // todo: send a notification to the user
  }


  /**
   * @brief Reverts to the specified version.
   * @param[in] $versionNumber (optional ) Reverts to the specified version. If a version is not specified it takes the
   * previous one.
   * @todo Implement the method Versionable.revert().
   */
  public function revert($versionNumber = NULL) {
    $this->meta['status'] = DocStatus::APPROVED;
  }


  /**
   * @brief Moves the document to the trash.
   */
  public function moveToTrash() {
    $this->meta['prevStatus'] = $this->meta['status'];
    $this->meta['status'] = DocStatus::DELETED;
    $this->meta['dustmanId'] = $this->user->id;
    $this->meta['deletedAt'] = time();
  }


  /**
   * @brief Restores the document to its previous status, removing it from trash.
   */
  public function restore() {
    // In case the document has been deleted, restore it to its previous status.
    $this->meta['status'] = $this->meta['prevStatus'];
    unset($this->meta['prevStatus']);
    unset($this->meta['dustmanId']);
    unset($this->meta['deletedAt']);
  }


  /**
   * @brief Gets information about all the previous versions.
   * @retval array
   */
  public function getPastVersionsInfo() {
    // todo
  }

  //@}


  /**
   * @copydoc Storable::save()
   */
  public function save() {
    // We force the document status in case it hasn't been changed.
    if ($this->isCreated())
      $this->meta["status"] = DocStatus::SUBMITTED;

    // Put your code here.
    parent::save();
  }


  /**
   * @brief Returns the author's username.
   */
  public function getUsername() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setKey($this->creatorId);
    return $this->couch->queryView("members", "allNames", NULL, $opts)[0]['value'][0];
  }


  /**
   * @brief Builds the gravatar uri.
   * @retval string
   */
  public function getGravatar() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setKey($this->creatorId);
    $email = $this->couch->queryView("members", "allNames", NULL, $opts)[0]['value'][1];
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


  public function getDustmanId() {
    return $this->meta['dustmanId'];
  }


  public function issetDustmanId() {
    return isset($this->meta['dustmanId']);
  }


  public function getDeletedAt() {
    return $this->meta['deletedAt'];
  }


  public function issetDeletedAt() {
    return isset($this->meta['deletedAt']);
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
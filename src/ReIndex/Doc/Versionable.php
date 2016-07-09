<?php

/**
 * @file Versionable.php
 * @brief This file contains the Versionable class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use EoC\Opt\ViewQueryOpts;

use ReIndex\Helper;
use ReIndex\Exception;
use ReIndex\Enum\State;
use ReIndex\Security\Role;
use ReIndex\Collection;
use ReIndex\Security\User\System;


/**
 * @brief A version of a content created by a user.
 * @nosubgrouping
 *
 * @cond HIDDEN_SYMBOLS
 *
 * @property string $unversionId           // [readonly] The id pruned of its version number.
 *
 * @property State $state                   // The state of the document.
 *
 * @property string $versionNumber         // The document version number.
 * @property string $previousVersionNumber // The version number of the previous document revision.
 *
 * @property string $username              // The author username.
 *
 * @property string $dustmanId             // [readonly] The user id of whom moved to trash the content.
 *
 * @property string $editSummary           // A brief explanation of an edit to a versionable content.
 *
 * @property Collection\VoteCollection $votes // Casted votes.
 *
 * @endcond
 */
abstract class Versionable extends ActiveDoc {

  private $votes; // Casted votes.
  private $state; // State of the document.


  /**
   * @brief Constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->votes = new Collection\VoteCollection($this);

    $this->state = new Version($this->meta);
    $this->state->set(State::CREATED);
  }


  /** @name Control Versioning Methods */
  //!@{

  /**
   * @brief Submits the document's revision for peer review.
   */
  public function submit() {
    if (!$this->user->has(new Role\MemberRole\SubmitRevisionPermission($this)))
      throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");

    $this->state->set(State::SUBMITTED);
  }


  /**
   * @brief Casts a vote to approve this document's revision.
   */
  public function approve() {
    if (!$value = $this->user->has(new Role\ReviewerRole\ApproveRevisionPermission($this)))
      throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");

    if ($this->user instanceof Member)
      $this->votes->cast($value, FALSE);

    if ($this->user instanceof System ||
        $this->votes->count(FALSE) >= $this->di['config']->review->scoreToApproveRevision) {
      $this->state->set(State::INDEXING);
      $this->save();
      // todo: send a notification to the user
    }
  }


  /**
   * @brief Casts a vote to rejects this document's revision.
   * @details The post will be automatically deleted in 10 days.
   * @param[in] $reason The reason why the revision has been rejected.
   */
  public function reject($reason) {
    if (!$value = $this->user->has(new Role\ReviewerRole\RejectRevisionPermission($this)))
      throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");

    if ($this->user instanceof Member)
      $this->votes->cast($value, FALSE, $reason);

    if ($this->user instanceof System ||
        $this->votes->count(FALSE) >= $this->di['config']->review->scoreToRejectRevision) {
      $this->state->set(State::REJECTED);
      $this->save();
      // todo: send a notification to the user
    }
  }


  /**
   * @brief Reverts to the specified version.
   * @param[in] $versionNumber (optional ) Reverts to the specified version. If a version is not specified it takes the
   * previous one.
   * @todo Implement the method Versionable.revert().
   */
  public function revert($versionNumber = NULL) {
    if (!$this->user->has(new Role\ModeratorRole\RevertToVersionPermission()))
      throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");

    $this->state->set(State::APPROVED);
  }


  /**
   * @brief Moves the document to the trash.
   */
  public function moveToTrash() {
    if (!$this->user->has(new Role\MemberRole\MoveRevisionToTrashPermission($this)))
      throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");

    $this->meta['prevstate'] = $this->state->get();
    $this->state->set(State::DELETED);
    $this->meta['dustmanId'] = $this->user->id;
    $this->meta['deletedAt'] = time();
  }


  /**
   * @brief Restores the document to its previous state, removing it from trash.
   */
  public function restore() {
    if (!$this->user->has(new Role\ModeratorRole\RestoreRevisionPermission($this)))
      throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");

    // In case the document has been deleted, restore it to its previous state.
    $this->state->set($this->meta['prevstate']);
    unset($this->meta['prevstate']);
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
   * @copydoc ActiveDoc::save()
   */
  public function save() {
    // We force the document state in case it hasn't been changed.
    if ($this->state->isCreated())
      $this->state->set(State::SUBMITTED);

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


  public function getState() {
    return $this->state;
  }


  public function issetState() {
    return isset($this->state);
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
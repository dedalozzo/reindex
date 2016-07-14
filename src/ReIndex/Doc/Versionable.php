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

  private $state; // State of the document.
  private $votes; // Casted votes.


  /**
   * @brief Constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->votes = new Collection\VoteCollection($this);

    $this->state = new State($this->meta);
    $this->state->set(State::CREATED);
  }


  /**
   * @brief Returns `true` in case there is an indexing task in progress, `false` otherwise.
   * @return bool
   */
  protected function indexingInProgress() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setKey($this->unversionId);
    $rows = $this->couch->queryView("revisions", "inProgress", NULL, $opts);

    return !$rows->isEmpty();
  }


  /** @name Control Versioning Methods */
  //!@{

  /**
   * @brief Submits the document's revision for peer review.
   */
  public function submit() {
    // Just in case the user is submitting a new revision for the current document, we need to create a new document.
    if ($this->state->is(State::CURRENT)) {
      // Appends a new version number to the ID.
      $this->setId($this->unversionId);

      // This is a new CouchDB document, so we needs to reset the rev number.
      $this->unsetRev();
    }

    // Finally saves the document itself.
    $this->save();
  }


  /**
   * @brief Casts a vote to approve this document's revision.
   */
  public function approve() {
    if (!$value = $this->user->has(new Role\MemberRole\ApproveRevisionPermission($this)))
      throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");

    if ($this->user instanceof Member)
      $this->votes->cast($value, FALSE);

    if (!$this->indexingInProgress() || $this->user instanceof System || $this->votes->count(FALSE) >= $this->di['config']->review->scoreToApproveRevision) {
      $this->state->set(State::INDEXING);
      $this->save();
    }
  }



  /**
   * @brief Casts a vote to rejects this document's revision.
   * @details The document's revision will be automatically deleted in 10 days.
   * @param[in] $reason The reason why the document's revision has been rejected.
   */
  public function reject($reason) {
    if (!$value = $this->user->has(new Role\ReviewerRole\RejectRevisionPermission($this)))
      throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");

    if ($this->user instanceof Member)
      $this->votes->cast($value, FALSE, '', $reason);

    if ($this->user instanceof System || $this->votes->count(FALSE) >= $this->di['config']->review->scoreToRejectRevision) {
      $this->state->set(State::REJECTED);
      $this->save();
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

    // cerca se la revisione specificata è approved e la marca come current.
  }


  /**
   * @brief Moves the document to the trash.
   */
  public function delete() {
    if (!$this->user->has(new Role\MemberRole\MoveToTrashPermission($this)))
      throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");

    if ($this->indexingInProgress())
      throw new Exception\InvalidStateException("Operazione non consentita; riprova più tardi.");

    $this->meta['prevState'] = $this->state->get();
    $this->meta['dustmanId'] = $this->user->id;
    $this->meta['deletedAt'] = time();

    $this->state->set(State::DELETING);

    $this->save();
  }


  /**
   * @brief Alias of delete().
   */
  public function moveToTrash() {
    $this->delete();
  }


  /**
   * @brief Restores the document to its previous state, removing it from trash.
   */
  public function restore() {
    if (!$this->user->has(new Role\ModeratorRole\RestorePermission($this)))
      throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");

    if ($this->meta['prevState'] === State::CURRENT)
      $this->state->set(State::DELETING);
    else
      $this->state->set($this->meta['prevState']);

    // In case the document has been deleted, restore it to its previous state.
    unset($this->meta['prevState']);
    unset($this->meta['dustmanId']);
    unset($this->meta['deletedAt']);

    $this->save();
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
    // We force the document's revision state in case it hasn't been changed.
    if ($this->state->is(State::CREATED))
      $this->state->set(State::SUBMITTED);

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
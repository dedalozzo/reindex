<?php

/**
 * @file Revision.php
 * @brief This file contains the Revision class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use EoC\Opt\ViewQueryOpts;

use ReIndex\Helper;
use ReIndex\Exception;
use ReIndex\Enum\State;
use ReIndex\Collection;
use ReIndex\Security\User\System;
use ReIndex\Security\Permission\IPermission;
use ReIndex\Security\Permission\Revision as Permission;


/**
 * @brief A version of a content created by a user.
 * @nosubgrouping
 *
 * @cond HIDDEN_SYMBOLS
 *
 * @property string $unversionId
 *
 * @property State $state
 *
 * @property string $versionNumber
 * @property string $previousVersionNumber
 *
 * @property string $username
 *
 * @property string $creatorId
 * @property string $editorId
 * @property string $dustmanId
 *
 * @property string $editSummary
 *
 * @property Collection\VoteCollection $votes // Casted votes.
 *
 * @endcond
 */
abstract class Revision extends ActiveDoc {

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
  abstract protected function indexingInProgress();


  /**
   * @brief Casts a vote valid as peer review.
   * @param[in] IPermission $permission A permission.
   * @param[in] bool $positive The vote can be positive or negative.
   * @param[in] string $reason The reason for the vote to be casted.
   */
  protected function castVoteForPeerReview(IPermission $permission, $positive = TRUE, $reason = '') {
    if (!$this->user->has($permission))
      throw new Exception\AccessDeniedException("Privilegi insufficienti o stato incompatibile.");

    if ($this->user instanceof Member) {
      $vote = $this->di['config']->review->{$permission->getRole()->getName().'Vote'};

      if ($positive) {
        $this->votes->cast($vote, FALSE, '', $reason);
        $this->markAsApproved();
      }
      else {
        $this->votes->cast(-$vote, FALSE, '', $reason);
        $this->markAsRejected();
      }
    }
  }


  /**
   * @brief Approves the document's revision.
   */
  abstract protected function markAsApproved();


  /**
   * @brief Rejects the document's revision.
   */
  protected function markAsRejected() {
    if ($this->user instanceof System ||
      $this->votes->count(FALSE) >= $this->di['config']->review->scoreToRejectRevision) {
      $this->state->set(State::REJECTED);
      $this->save();
    }
  }


  /** @name Control Versioning Methods */
  //!@{

  /**
   * @brief Resets the document's identifier and unset its revision.
   */
  protected function reset() {
    // Appends a new version number to the ID.
    $this->setId($this->unversionId);

    // This is a new CouchDB document, so we needs to reset the rev number.
    $this->unsetRev();
  }


  /**
   * @brief Submits the document's revision for peer review.
   */
  protected function submit() {
    // In case this is a revision of a published version, we must update the editor identifier.
    if ($this->state->is(State::CURRENT) && $this->user instanceof Member) {
      $this->editorId = $this->user->id;
      $this->reset();
    }

    $this->state->set(State::SUBMITTED);

    // Tries to approve the revision; in case of a failure, submits it.
    try {
      $this->approve();
    }
    catch (Exception\AccessDeniedException $e) {
      $this->save();
    }
  }


  /**
   * @brief Casts a vote to approve this document's revision.
   */
  public function approve() {
    $this->castVoteForPeerReview(new Permission\ApprovePermission($this));
  }


  /**
   * @brief Casts a vote to reject this document's revision.
   * @param[in] string $reason The reason why the document's revision has been rejected.
   */
  public function reject($reason) {
    $this->castVoteForPeerReview(new Permission\RejectPermission($this), $reason);
  }


  /**
   * @brief Reverts to the specified version.
   * @param[in] $versionNumber (optional ) Reverts to the specified version. If a version is not specified it takes the
   * previous one.
   * @todo Implement the method Revision.revert().
   */
  protected function revert($versionNumber = NULL) {
    if (!$this->user->has(new Permission\RevertPermission($this)))
      throw new Exception\AccessDeniedException("Privilegi insufficienti o stato incompatibile.");

    // cerca se la revisione specificata è approved e la marca come current.
  }


  /**
   * @brief Alias of delete().
   */
  protected function moveToTrash() {
    if (!$this->user->has(new Permission\MoveToTrashPermission($this)))
      throw new Exception\AccessDeniedException("Privilegi insufficienti o stato incompatibile.");

    if ($this->indexingInProgress())
      throw new Exception\InvalidStateException("Operazione non consentita; riprova più tardi.");

    $this->meta['prevState'] = $this->state->get();
    $this->meta['dustmanId'] = $this->user->id;
    $this->meta['deletedAt'] = time();
  }


  /**
   * @brief Restores the document to its previous state, removing it from trash.
   */
  protected function restore() {
    if (!$this->user->has(new Permission\RestorePermission($this)))
      throw new Exception\AccessDeniedException("Privilegi insufficienti o stato incompatibile.");

    if ($this->meta['prevState'] === State::CURRENT)
      $this->state->set(State::INDEXING);
    else
      $this->state->set($this->meta['prevState']);

    // In case the document has been deleted, restore it to its previous state.
    unset($this->meta['prevState']);
    unset($this->meta['dustmanId']);
    unset($this->meta['deletedAt']);
  }

  //@}


  /**
   * @brief A revision document can't be deleted, but it can be moved into the trash.
   */
  public function delete() {
    throw new \BadMethodCallException("You can't call this method on a revision object.");
  }


  /**
   * @copydoc ActiveDoc::save()
   */
  public function save($update = TRUE) {
    $userId = $this->user->getId();

    // Creator ID has not been provided.
    if (!isset($this->creatorId) && isset($userId))
      $this->creatorId = $userId;

    // We force the document's revision state in case it hasn't been changed.
    if ($this->state->is(State::CREATED))
      $this->state->set(State::SUBMITTED);

    parent::save($update);
  }


  /**
   * @brief Returns the author's username.
   */
  public function getUsername() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setKey($this->creatorId);
    // members/names/view
    return $this->couch->queryView('members', 'names', 'view', NULL, $opts)[0]['value'][0];
  }


  /**
   * @brief Builds the gravatar uri.
   * @retval string
   */
  public function getGravatar() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setKey($this->creatorId);
    $email = $this->couch->queryView('members', 'names', 'view', NULL, $opts)[0]['value'][1];
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


  public function getVotes() {
    return $this->votes;
  }


  public function issetVotes() {
    return isset($this->votes);
  }

  //! @endcond

}
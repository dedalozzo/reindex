<?php

/**
 * @file Revision.php
 * @brief This file contains the Revision class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use EoC\Opt\ViewQueryOpts;

use ToolBag\Helper;

use ReIndex\Enum\State;
use ReIndex\Security\Permission\Revision as Permission;
use ReIndex\Controller\BaseController;

use Daikengo\Permission\IPermission;
use Daikengo\Exception\AccessDeniedException;


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
 * @property string $toc
 *
 * @property array $data
 *
 * @property string $versionNumber
 * @property string $previousVersionNumber
 *
 * @property string $editorId
 * @property string $dustmanId
 *
 * @property string $editSummary
 *
 * @endcond
 */
abstract class Revision extends Content {

  private $state;


  /**
   * @brief Constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->state = new State($this->meta);
    $this->state->set(State::CREATED);
  }


  /**
   * @brief Executed when the user is displaying a document's revision.
   * @param[in] BaseController $controller A controller instance.
   */
  protected function viewAction(BaseController $controller) {
    if (!$this->user->has(new Permission\ViewPermission($this)))
      return $controller->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);
  }


  /**
   * @copydoc Content::parseBody()
   */
  public function parseBody() {
    $metadata = [];
    $this->html = $this->markdown->parse($this->body, $metadata);
    $this->toc = !empty($metadata['toc']) ? $metadata['toc'] : NULL;
    $this->data = is_array($metadata['meta']) ? $metadata['meta'] : NULL;
  }


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
    // We force the document's revision state in case it hasn't been changed.
    if ($this->state->is(State::CREATED))
      $this->state->set(State::SUBMITTED);

    parent::save($update);
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
   * @brief Returns `true` if the revision can be approved instantly, `false` if a peer review is necessary.
   * @retval bool
   */
  protected function instantApproval() {
    return $this->user->has(new Permission\ApprovePermission($this));
  }


  /**
   * @brief Casts a vote valid as peer review.
   * @param[in] IPermission $permission A permission.
   * @param[in] string $reason The user must provide a reason exclusively for a negative vote.
   */
  protected function castVoteForPeerReview(IPermission $permission, $reason = '') {
    if (!$this->user->has($permission))
      throw new AccessDeniedException("Insufficient privileges or illegal state.");

    if ($this->user instanceof Member) {
      $vote = $this->di['config']['peer-review']->{$permission->getRole()->getName().'Vote'};

      if (empty($reason)) {
        $this->votes->cast($vote, FALSE);

        if ($this->votes->count(FALSE) >= $this->di['config']['peer-review']->score)
          $this->replaceCurrentRevision();
      }
      else {
        $this->votes->cast(-$vote, FALSE, '', $reason);

        if ($this->votes->count(FALSE) <= -$this->di['config']->review->score)
          $this->state->set(State::REJECTED);
      }
    }
  }


  /**
   * @brief Cancels the current revision and sets its state to `approved`.
   */
  protected function cancelCurrentRevision() {
    // It's a new document, there is no need to check for a current revision.
    if (is_null($this->rev))
      return;

    $dbName = $this->getDbName();

    // Sets the state of the current revision to `approved`.
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setKey($this->unversionId);
    // posts/byUnversionId/view
    $rows = $this->couch->queryView($dbName, 'byUnversionId', 'view', NULL, $opts);

    if (!$rows->isEmpty()) {
      $current = $this->couch->getDoc($dbName, Couch::STD_DOC_PATH, $rows[0]['id']);
      $current->state->set(State::APPROVED);
      $current->tasks->remove(new IndexPostTask($current));
      $current->save(FALSE);
    }
  }


  /**
   * @brief Replaces the current revision with this one.
   * @details It also marks the current revision as `approved`.
   */
  protected function replaceCurrentRevision() {
    $this->cancelCurrentRevision();
    $this->state->set(State::CURRENT);
  }


  /**
   * @brief Submits the document's revision for peer review.
   */
  protected function submit() {
    // In case this is the current revision, we must update the `editorId`.
    if ($this->state->is(State::CURRENT) && $this->user instanceof Member)
      $this->editorId = $this->user->id;

    $this->parseBody();

    // We must change the state here, otherwise `instantApproval()` will return `false`.
    $this->state->set(State::SUBMITTED);

    if ($this->instantApproval())
      $this->replaceCurrentRevision();

    // Resets the document's identifier and unset its CouchDB's revision.
    $this->reset();
  }


  /**
   * @brief Approves this document's revision.
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
      throw new AccessDeniedException("Insufficient privileges or illegal state.");

    // cerca se la revisione specificata è approved e la marca come current.
  }


  /**
   * @brief Moves the document's revision to the trash.
   */
  public function moveToTrash() {
    if (!$this->user->has(new Permission\MoveToTrashPermission($this)))
      throw new AccessDeniedException("Insufficient privileges or illegal state.");

    $this->meta['prevState'] = $this->state->get();
    $this->meta['dustmanId'] = $this->user->id;
    $this->meta['deletedAt'] = time();

    $this->state->set(State::DELETED);
  }


  /**
   * @brief Restores the document to its previous state, removing it from trash.
   */
  public function restore() {
    if (!$this->user->has(new Permission\RestorePermission($this)))
      throw new AccessDeniedException("Insufficient privileges or illegal state.");

    $this->state->set($this->meta['prevState']);

    // In case the document has been deleted, restore it to its previous state.
    unset($this->meta['prevState']);
    unset($this->meta['dustmanId']);
    unset($this->meta['deletedAt']);
  }

  //@}


  //! @cond HIDDEN_SYMBOLS

  public function setId($value) {
    $pos = stripos($value, Helper\TextHelper::SEPARATOR);
    $this->meta['unversionId'] = Helper\TextHelper::unversion($value);
    $this->meta['versionNumber'] = ($pos) ? substr($value, $pos + strlen(Helper\TextHelper::SEPARATOR)) : (string)time();
    $this->meta['_id'] = $this->meta['unversionId'] . Helper\TextHelper::SEPARATOR . $this->meta['versionNumber'];
  }


  public function getUnversionId() {
    return $this->meta["unversionId"];
  }


  public function issetUnversionId() {
    return isset($this->meta["unversionId"]);
  }


  public function getState() {
    return $this->state;
  }


  public function issetState() {
    return isset($this->state);
  }


  public function getToc() {
    return $this->toc;
  }


  public function issetToc() {
    return isset($this->toc);
  }


  public function getData() {
    return $this->data;
  }


  public function issetData() {
    return isset($this->data);
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
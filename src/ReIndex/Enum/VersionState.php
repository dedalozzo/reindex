<?php

/**
 * @file VersionState.php
 * @brief This file contains the VersionState enumerator.
 * @details
 * @author Filippo F. Fadda
 */


//! Enumerators
namespace ReIndex\Enum;


/**
 * @brief Different states a post may assume.
 */
class VersionState {

  protected $meta;

  const CREATED = "created"; //!< The document has been created.
  const CURRENT = "current"; //!< The current document revision.
  const DRAFT = "draft"; //!< The document can eventually be saved as draft.
  const APPROVED = "approved"; //!< The document revision has been approved by peers.
  const SUBMITTED = "submitted"; //!< The document has been submitted for publishing.
  const RETURNED = "returned"; //!< The document has been returned for revision.
  const REJECTED = "rejected"; //!< The document revision has been rejected.
  const DELETED = "deleted"; //!< The document has been deleted.


  /**
   * @brief Creates a new object.
   * @param[in] array $meta Array of metadata.
   */
  public function __construct(array &$meta) {
    $this->meta = &$meta;
  }


  /**
   * @brief Returns `true` in case the provided state matches the current version state.
   * @param[in] string $state The state to check.
   * @retval bool
   */
  protected function check($state) {
    return ($this->meta["state"] === $state) ? TRUE : FALSE;
  }


  /**
   * @brief Returns the version state.
   * @retval string
   */
  public function get() {
    return $this->meta["state"];
  }


  /**
   * @brief Returns `true` in case the provided state matches the current version state.
   * @param[in] string $state The state to set.
   */
  public function set($state) {
    $this->meta["state"] = $state;
  }


  /**
   * @brief Returns `true` if this document has just been created, `false` otherwise.
   * @retval bool
   */
  public function isCreated() {
    return $this->check(VersionState::CREATED);
  }


  /**
   * @brief Returns `true` if this document is only a draft, `false` otherwise.
   * @retval bool
   */
  public function isDraft() {
    return $this->check(VersionState::DRAFT);
  }


  /**
   * @brief Returns `true` if this document revision is the current one, `false` otherwise.
   * @retval bool
   */
  public function isCurrent() {
    return $this->check(VersionState::CURRENT);
  }


  /**
   * @brief Returns `true` if this document has been put into the trash, `false` otherwise.
   * @retval bool
   */
  public function isMovedToTrash() {
    return $this->check(VersionState::DELETED);
  }


  /**
   * @brief Returns `true` if this document has been submitted for peer review, `false` otherwise.
   * @retval bool
   */
  public function isSubmittedForPeerReview() {
    return $this->check(VersionState::SUBMITTED);
  }


  /**
   * @brief Returns `true` if this document revision has been approved, `false` otherwise.
   * @retval bool
   */
  public function isApproved() {
    return $this->check(VersionState::APPROVED);
  }


  /**
   * @brief Returns `true` if this document revision has been rejected, `false` otherwise.
   * @retval bool
   */
  public function isRejected() {
    return $this->check(VersionState::REJECTED);
  }


  /**
   * @brief Returns `true` if this document has been returned for revision, `false` otherwise.
   * @retval bool
   */
  public function isReturnedForRevision() {
    return $this->check(VersionState::RETURNED);
  }

}
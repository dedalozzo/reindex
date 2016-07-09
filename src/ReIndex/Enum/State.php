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

  const CREATED = "created"; //!< A document's revision has been created.
  const CURRENT = "current"; //!< The current document's revision.
  const DRAFT = "draft"; //!< The document's revision can eventually be saved as draft.
  const APPROVED = "approved"; //!< The document's revision has been approved by peers.
  const SUBMITTED = "submitted"; //!< The document's revision has been submitted for peer review.
  const REJECTED = "rejected"; //!< The document's revision has been rejected.
  const DELETED = "deleted"; //!< The document's revision has been deleted.
  const INDEXING = "indexing"; //!< The document's revision needs to be indexed.


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
  public function is($state) {
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

}
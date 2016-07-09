<?php

/**
 * @file State.php
 * @brief This file contains the State enumerator.
 * @details
 * @author Filippo F. Fadda
 */


//! Enumerators
namespace ReIndex\Enum;


/**
 * @brief Different states a post may assume.
 */
class State {

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
   * @brief Returns `true` in case the provided value matches the current version's state.
   * @param[in] string $value The value to check.
   * @retval bool
   */
  public function is($value) {
    return ($this->meta["state"] === $value) ? TRUE : FALSE;
  }


  /**
   * @brief Returns the version's state.
   * @retval string
   */
  public function get() {
    return $this->meta["state"];
  }


  /**
   * @brief Returns `true` in case the provided state matches the current version state.
   * @param[in] string $value The value to set.
   */
  public function set($value) {
    $this->meta["state"] = $value;
  }

}
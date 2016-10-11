<?php

/**
 * @file State.php
 * @brief This file contains the State enumerator.
 * @details
 * @author Filippo F. Fadda
 */


//! Enumerators
namespace ReIndex\Enum;
use ReIndex\Exception\InvalidStateException;


/**
 * @brief Different states a post may assume.
 */
class State {

  protected $meta;

  // DO NOT CHANGE THE CONSTANTS' VALUES!
  const CURRENT     = 1;   //!< Indicates this is the current document's revision.
  const CREATED     = 2;   //!< A document's revision has been created.
  const DRAFT       = 4;   //!< The document's revision can eventually be saved as draft.
  const APPROVED    = 8;   //!< The document's revision has been approved by peers.
  const SUBMITTED   = 16;  //!< The document's revision has been submitted for peer review.
  const REJECTED    = 32;  //!< The document's revision has been rejected.
  const DELETED     = 64;  //!< The document's revision has been deleted.
  const IMPORTED    = 128; //!< The document's revision has been imported.

  const INDEXING    = 256; //!< The document's revision must be indexed.


  /**
   * @brief Creates a new object.
   * @param[in] array $meta Array of metadata.
   */
  public function __construct(array &$meta) {
    $this->meta = &$meta;
  }


  /**
   * @brief Returns `true` in case the provided value matches the state, `false` otherwise.
   * @param[in] string $value The value to check.
   * @retval bool
   */
  public function is($value) {
    return ($this->meta["state"] & $value);
  }


  /**
   * @brief Alias of `is()` used inside a Volt template, because of a bug.
   */
  public function equal($value) {
    return $this->is($value);
  }


  /**
   * @brief Returns the version's state.
   * @retval string
   */
  public function get() {
    return $this->meta["state"];
  }


  /**
   * @brief Sets the state to the provided value.
   * @param[in] string $value The value to set.
   */
  public function set($value) {

    switch ($value) {
      case self::CURRENT: $this->meta["state"] = $value; break;
      case self::CREATED: $this->meta["state"] = $value; break;
      case self::DRAFT: $this->meta["state"] = $value; break;
      case self::APPROVED: $this->meta["state"] = $value; break;
      case self::SUBMITTED: $this->meta["state"] = $value; break;
      case self::REJECTED: $this->meta["state"] = $value; break;
      case self::DELETED: $this->meta["state"] = $value; break;
      case (self::IMPORTED | self::INDEXING): $this->meta["state"] = $value; break;
      case (self::CURRENT | self::INDEXING): $this->meta["state"] = $value; break;
      case (self::DELETED | self::INDEXING): $this->meta["state"] = $value; break;
      default: throw new InvalidStateException('Invalid state.');
    }

  }


  /**
   * @brief Removes the provided value from the set.
   * @param[in] string $value The value to unset.
   */
  public function remove($value) {
    $this->meta["state"] &= ~$value;
  }

}
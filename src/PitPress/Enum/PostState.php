<?php

/**
 * @file PostState.php
 * @brief This file contains the PostState enumerator.
 * @details
 * @author Filippo F. Fadda
 */


//! Enumerators namespace.
namespace PitPress\Enum;


/**
 * @brief Different states a post may assume.
 */
class PostState {
  const DRAFT_STATE = "draft"; //!< The post can eventually be saved as draft.
  const SUBMITTED = "submitted"; //!< The item has been submitted for publishing.
  const PUBLISHED = "published"; //!< The item has been published.
  const REJECTED = "rejected"; //!< The item has been rejected.
}
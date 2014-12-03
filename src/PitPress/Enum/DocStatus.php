<?php

/**
 * @file DocStatus.php
 * @brief This file contains the DocStatus enumerator.
 * @details
 * @author Filippo F. Fadda
 */


//! Enumerators namespace.
namespace PitPress\Enum;


/**
 * @brief Different states a post may assume.
 */
class DocStatus {
  const CREATED = "created"; //!< The document has been created.
  const CURRENT = "current"; //!< The current document revision.
  const DRAFT = "draft"; //!< The document can eventually be saved as draft.
  const APPROVED = "approved"; //!< The document revision has been approved by peers.
  const SUBMITTED = "submitted"; //!< The document has been submitted for publishing.
  const RETURNED = "returned"; //!< The document has been returned for revision.
  const REJECTED = "rejected"; //!< The document revision has been rejected.
  const DELETED = "deleted"; //!< The document has been deleted.
}
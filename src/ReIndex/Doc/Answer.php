<?php

/**
 * @file Answer.php
 * @brief This file contains the Answer class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use ReIndex\Property\TBody;
use ReIndex\Collection;


/**
 * @brief An answer.
 * @nosubgrouping
 *
 * @cond HIDDEN_SYMBOLS
 *
 * @property string $questionId
 *
 * @property Collection\VoteCollection $votes
 *
 * @endcond
 */
final class Answer extends Revision {
  use TBody;


  public function __construct() {
    parent::__construct();
  }


  /**
   * @copydoc ActiveDoc::getDbName()
   */
  protected function getDbName() {
    return 'answers';
  }


  /**
   * @brief Marks the item as duplicate of another item.
   */
  public function markAsDuplicate() {

  }

  //! @cond HIDDEN_SYMBOLS

  public function getPostId() {
    return $this->meta['postId'];
  }


  public function issetPostId() {
    return isset($this->meta['postId']);
  }


  public function setPostId($value) {
    $this->meta['postId'] = $value;
  }


  public function unsetPostId() {
    if ($this->isMetadataPresent('postId'))
      unset($this->meta['postId']);
  }

  //! @endcond

}
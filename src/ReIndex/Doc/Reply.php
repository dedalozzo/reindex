<?php

/**
 * @file Reply.php
 * @brief This file contains the Reply class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use ReIndex\Property;
use ReIndex\Collection;


/**
 * @brief A generic reply. It can be an answer, a comment to a question (or to an answer) or a reply to a post (an
 * article, a book review, etc.).
 * @nosubgrouping
 *
 * @cond HIDDEN_SYMBOLS
 *
 * @property Collection\VoteCollection $votes // Casted votes.
 *
 * @endcond
 */
class Reply extends Versionable {
  use Property\TBody;

  private $votes; // Casted votes.


  public function __construct() {
    parent::__construct();
    $this->votes = new Collection\VoteCollection($this);
  }


  public function save() {
    $this->meta['supertype'] = 'reply';

    parent::save();
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


  public function getVotes() {
    return $this->votes;
  }


  public function issetVotes() {
    return isset($this->votes);
  }

  //! @endcond

} 
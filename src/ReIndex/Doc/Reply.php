<?php

/**
 * @file Reply.php
 * @brief This file contains the Reply class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use ReIndex\Property\TBody;
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
  use TBody;


  public function __construct() {
    parent::__construct();

    // Since we can't use reflection inside EoC Server, we need a way to recognize every subclass of the `Reply` class.
    // This is done testing `isset($doc->supertype) && $doc->supertype == 'reply'`.
    $this->meta['supertype'] = 'reply';
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
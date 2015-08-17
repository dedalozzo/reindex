<?php

/**
 * @file Reply.php
 * @brief This file contains the Reply class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Model;


use ReIndex\Extension;
use ReIndex\Property;


/**
 * @brief A generic reply. It can be an answer, a comment to a question (or to an answer) or a reply to a post (an
 * article, a book review, etc.).
 * @nosubgrouping
 */
class Reply extends Versionable implements Extension\IVote {
  use Extension\TVote;
  use Property\TBody;


  /**
   * @param[in] $bypass When `true` calls directly the parent method.
   */
  public function save($bypass = FALSE) {
    if (!$bypass)
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

  //! @endcond

} 
<?php

/**
 * @file Comment.php
 * @brief This file contains the Comment class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Model;


/**
 * @brief A comment is only used on questions and answers. A comment is a way to ask more information to the question's
 * author, to be able to provide the right answer to the question. All the comments are listed below the question (or
 * the answer) before any answers is showed.
 * @nosubgrouping
 */
class Comment extends Storable {


  //! @cond HIDDEN_SYMBOLS

  public function getItemId() {
    return $this->meta['itemId'];
  }


  public function issetItemId() {
    return isset($this->meta['itemId']);
  }


  public function setItemId($value) {
    $this->meta['itemId'] = $value;
  }


  public function unsetItemId() {
    if ($this->isMetadataPresent('itemId'))
      unset($this->meta['itemId']);
  }

  //! @endcond


}



<?php

//! @file Replay.php
//! @brief This file contains the Replay class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model;


use PitPress\Extension;
use PitPress\Property;


class Replay extends Item implements Extension\IVote, Extension\IVersion {
  use Extension\TVote, Extension\TVersion;
  use Property\TBody;


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
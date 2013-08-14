<?php

//! @file Excerpt.php
//! @brief This file contains the Excerpt trait.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Property;


//! @cond HIDDEN_SYMBOLS
trait Excerpt {


  public function getExcerpt() {
    return $this->meta['excerpt'];
  }


  public function issetExcerpt() {
    return isset($this->meta['excerpt']);
  }


  public function setExcerpt($value) {
    $this->meta["excerpt"] = $value;
  }


  public function unsetExcerpt() {
    if ($this->isMetadataPresent('excerpt'))
      unset($this->meta['excerpt']);
  }

}
//! @endcond
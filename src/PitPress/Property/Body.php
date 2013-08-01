<?php

//! @file Body.php
//! @brief This file contains the Body trait.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Property;


//! @cond HIDDEN_SYMBOLS
trait Body {

  public function getBody() {
    return $this->meta["body"];
  }


  public function issetBody() {
    return isset($this->meta['body']);
  }


  public function setBody($value) {
    $this->meta["body"] = $value;
  }


  public function unsetBody() {
    if ($this->isMetadataPresent('body'))
      unset($this->meta['body']);
  }

}
//! @endcond
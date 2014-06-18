<?php

/**
 * @file TBody.php
 * @brief This file contains the TBody trait.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Property;


//! @cond HIDDEN_SYMBOLS
trait TBody {

  public function getBody() {
    return $this->meta["body"];
  }


  public function issetBody() {
    return isset($this->meta['body']);
  }


  public function setBody($value) {
    $this->meta["body"] = trim($value);
  }


  public function unsetBody() {
    if ($this->isMetadataPresent('body'))
      unset($this->meta['body']);
  }


  public function getHtml() {
    return $this->meta["html"];
  }


  public function issetHtml() {
    return isset($this->meta['html']);
  }


  public function setHtml($value) {
    $this->meta["html"] = $value;
  }


  public function unsetHtml() {
    if ($this->isMetadataPresent('html'))
      unset($this->meta['html']);
  }
  
}
//! @endcond
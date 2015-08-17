<?php

/**
 * @file TDescription.php
 * @brief This file contains the TDescription trait.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Property;


//! @cond HIDDEN_SYMBOLS
trait TDescription {


  public function getDescription() {
    return $this->meta['description'];
  }


  public function issetDescription() {
    return isset($this->meta['description']);
  }


  public function setDescription($value) {
    $this->meta["description"] = $value;
  }


  public function unsetDescription() {
    if ($this->isMetadataPresent('description'))
      unset($this->meta['description']);
  }

}
//! @endcond
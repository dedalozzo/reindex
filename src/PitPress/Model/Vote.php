<?php

/**
 * @file Vote.php
 * @brief This file contains the Vote class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model;


/**
 * @brief This class is used to keep trace of the user votes.
 * @nosubgrouping
 */
class Vote extends Storable { //implements ISubject {

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


  public function getUserId() {
    return $this->meta['userId'];
  }


  public function issetUserId() {
    return isset($this->meta['userId']);
  }


  public function setUserId($value) {
    $this->meta['userId'] = $value;
  }


  public function unsetUserId() {
    if ($this->isMetadataPresent('userId'))
      unset($this->meta['userId']);
  }


  public function getValue() {
    return $this->meta['value'];
  }


  public function issetValue() {
    return isset($this->meta['value']);
  }


  public function setValue($value) {
    $this->meta['value'] = $value;
  }


  public function unsetValue() {
    if ($this->isMetadataPresent('value'))
      unset($this->meta['value']);
  }

  //! @endcond

}
<?php

/**
 * @file Review.php
 * @brief This file contains the Review class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Model;


/**
 * @brief A user's review, of a book for example.
 * @nosubgrouping
 */
class Review extends Reply {


  //! @cond HIDDEN_SYMBOLS

  public function getPositive() {
    return $this->meta['positive'];
  }


  public function issetPositive() {
    return isset($this->meta['positive']);
  }


  public function setPositive($value) {
    $this->meta['positive'] = $value;
  }


  public function unsetPositive() {
    if ($this->isMetadataPresent('positive'))
      unset($this->meta['positive']);
  }


  public function getPositiveHtml() {
    return $this->meta['positiveHtml'];
  }


  public function issetPositiveHtml() {
    return isset($this->meta['positiveHtml']);
  }


  public function setPositiveHtml($value) {
    $this->meta['positiveHtml'] = $value;
  }


  public function unsetPositiveHtml() {
    if ($this->isMetadataPresent('positiveHtml'))
      unset($this->meta['positiveHtml']);
  }


  public function getNegative() {
    return $this->meta['negative'];
  }


  public function issetNegative() {
    return isset($this->meta['negative']);
  }


  public function setNegative($value) {
    $this->meta['negative'] = $value;
  }


  public function unsetNegative() {
    if ($this->isMetadataPresent('negative'))
      unset($this->meta['negative']);
  }


  public function getNegativeHtml() {
    return $this->meta['negativeHtml'];
  }


  public function issetNegativeHtml() {
    return isset($this->meta['negativeHtml']);
  }


  public function setNegativeHtml($value) {
    $this->meta['negativeHtml'] = $value;
  }


  public function unsetNegativeHtml() {
    if ($this->isMetadataPresent('negativeHtml'))
      unset($this->meta['negativeHtml']);
  }

  //! @endcond

}
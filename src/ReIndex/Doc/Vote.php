<?php

/**
 * @file Vote.php
 * @brief This file contains the Vote class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use EoC\Doc\Doc;


/**
 * @brief This class is used to keep trace of the user votes.
 * @nosubgrouping
 */
final class Vote extends Doc {


  /**
   * @brief Creates an instance of Vote class.
   * @param[in] string $itemId The ID of the document the vote refers.
   * @param[in] string $voterId The ID of the member who has voted.
   * @param[in] int $value The vote's value.
   */
  public static function cast($itemId, $voterId, $value = 1) {
    $instance = new self();

    $instance->meta["itemId"] = $itemId;
    $instance->meta["voterId"] = $voterId;
    $instance->meta["value"] = $value;
    $instance->meta["timestamp"] = time();

    return $instance;
  }


  //! @cond HIDDEN_SYMBOLS

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


  public function getTimestamp() {
    return $this->meta['timestamp'];
  }


  public function issetTimestamp() {
    return isset($this->meta['timestamp']);
  }


  public function setTimestamp($timestamp) {
    $this->meta['timestamp'] = $timestamp;
  }


  public function unsetTimestamp() {
    if ($this->isMetadataPresent('timestamp'))
      unset($this->meta['timestamp']);
  }

  //! @endcond

}
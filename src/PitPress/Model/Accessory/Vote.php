<?php

/**
 * @file Vote.php
 * @brief This file contains the Vote class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Accessory;


use PitPress\Model\Storable;


/**
 * @brief This class is used to keep trace of the user votes.
 * @nosubgrouping
 */
class Vote extends Storable {

  /**
   * @brief Creates an instance of Vote class.
   * @param[in] $itemId The item identifier.
   * @param[in] $userId The identifier of the user who has voted.
   * @param[in] $value The value of the vote.
   * @return Vote
   */
  public static function create($itemId, $userId, $value) {
    $instance = new self();

    $instance->meta["itemId"] = $itemId;
    $instance->meta["userId"] = $userId;
    $instance->setValue($value);

    return $instance;
  }


  //! @cond HIDDEN_SYMBOLS

  public function getItemId() {
    return $this->meta["itemId"];
  }


  public function getUserId() {
    return $this->meta["userid"];
  }


  public function getValue() {
    return $this->meta["value"];
  }


  public function setValue($value) {
    $this->meta["value"] = $value;
  }

  //! @endcond

}
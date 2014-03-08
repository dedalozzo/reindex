<?php

//! @file Reputation.php
//! @brief This file contains the Reputation class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\Accessory;


use ElephantOnCouch\Doc\Doc;


//! @brief This class is used to keep trace of the user reputation.
//! @nosubgrouping
class Reputation extends Doc {


  //! @brief Creates an instance of Reputation class.
  public static function create($userId, $itemId, $points, $timestamp = NULL) {
    $instance = new self();

    $instance->meta["userId"] = $userId;
    $instance->meta["itemId"] = $itemId;
    $instance->meta["points"] = $points;

    if (is_null($timestamp))
      $instance->meta["timestamp"] = time();

    return $instance;
  }

} 
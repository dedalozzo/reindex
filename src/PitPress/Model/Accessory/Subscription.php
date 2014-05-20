<?php

//! @file Subscription.php
//! @brief This file contains the Subscription class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\Accessory;


use ElephantOnCouch\Doc\Doc;


//! @brief This class is used to keep trace of the user subscriptions.
//! @nosubgrouping
class Subscription extends Doc {

  //! @brief Creates an instance of Star class.
  public static function create($userId, $itemId, $timestamp = NULL) {
    $instance = new self();

    $instance->meta["userId"] = $userId;
    $instance->meta["itemId"] = $itemId;

    if (is_null($timestamp))
      $instance->meta["timestamp"] = time();

    return $instance;
  }

}
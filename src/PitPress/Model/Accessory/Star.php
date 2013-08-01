<?php

//! @file Star.php
//! @brief This file contains the Star class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\Accessory;


use ElephantOnCouch\Doc\Doc;


//! @brief This class is used to keep trace of the user favourites.
//! @nosubgrouping
class Star extends Doc {

  //! @brief Creates an instance of Star class.
  public static function create($itemId, $userId, $timestamp = NULL) {
    $instance = new self();

    $instance->meta["itemId"] = $itemId;
    $instance->meta["userId"] = $userId;

    if (is_null($timestamp))
      $instance->meta["timestamp"] = time();

    return $instance;
  }

}
<?php

/**
 * @file Subscription.php
 * @brief This file contains the Subscription class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use ReIndex\Helper\Text;

use EoC\Doc\Doc;


/**
 * @brief This class is used to keep trace of a member's subscriptions.
 * @nosubgrouping
 */
class Subscription extends Doc {


  /**
   * @brief Creates an instance of Subscription class.
   */
  public static function create($itemId, $memberId, $timestamp = NULL) {
    $instance = new self();

    $instance->meta["itemId"] = Text::unversion($itemId);
    $instance->meta["memberId"] = $memberId;

    if (is_null($timestamp))
      $instance->meta["timestamp"] = time();
    else
      $instance->meta["timestamp"] = $timestamp;

    return $instance;
  }

}
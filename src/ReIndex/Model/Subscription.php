<?php

/**
 * @file Subscription.php
 * @brief This file contains the Subscription class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Model;


use ReIndex\Feature\Subscribable;
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
  public static function create(Subscribable $item, Member $member, $timestamp = NULL) {
    $instance = new self();

    $instance->meta["userId"] = $member->id;
    $instance->meta["itemId"] = Text::unversion($item->getId());

    if (is_null($timestamp))
      $instance->meta["timestamp"] = time();
    else
      $instance->meta["timestamp"] = $timestamp;

    return $instance;
  }

}
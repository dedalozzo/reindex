<?php

/**
 * @file Follower.php
 * @brief This file contains the Follower class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use EoC\Doc\Doc;


/**
 * @brief This class is used to represent a follower.
 * @nosubgrouping
 */
class Follower extends Doc {


  /**
   * @brief Creates an instance of Follower class.
   * @param[in] string $memberId The sender ID of the member requested the friendship.
   * @param[in] string $followerId The receiver ID of the member requested the friendship.
   */
  public static function create($memberId, $followerId) {
    $instance = new self();

    $instance->meta["memberId"] = $memberId;
    $instance->meta["followerId"] = $followerId;

    return $instance;
  }

}
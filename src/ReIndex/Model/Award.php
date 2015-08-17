<?php

/**
 * @file Award.php
 * @brief This file contains the Award class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Model;


use EoC\Doc\Doc;


/**
 * @brief This class is used to keep trace of user awards.
 * @details An award can be assigned once or multiple times, depends of the badge type.
 * @nosubgrouping
 */
class Award extends Doc {

  /**
   * @brief Creates an instance of Award class.
   * @param[in] string $userId The user ID.
   * @param[in] string $badge The awarded badge's name.
   * @retval Award
   */
  public static function create($userId, $badge) {
    $instance = new self();

    $instance->meta["userId"] = $userId;
    $instance->meta["badgeClass"] = $badge;
    $instance->meta["timestamp"] = time();

    return $instance;
  }

} 
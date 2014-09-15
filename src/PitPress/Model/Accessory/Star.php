<?php

/**
 * @file Star.php
 * @brief This file contains the Star class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Accessory;


use ElephantOnCouch\Doc\Doc;

use PitPress\Model\Versionable;


/**
 * @brief This class is used to keep trace of the user favourites.
 * @nosubgrouping
 */
class Star extends Doc {

  /**
   * @brief Creates an instance of Star class.
   */
  public static function create($userId, Versionable $item, $timestamp = NULL) {
    $instance = new self();

    $instance->meta["userId"] = $userId;
    $instance->meta["itemId"] = $item->getUnversionId();
    $instance->meta["itemType"] = $item->type;
    $instance->meta["itemPublishedAt"] = $item->publishedAt;

    if (is_null($timestamp))
      $instance->meta["addedAt"] = time();
    else
      $instance->meta["addedAt"] = $timestamp;

    return $instance;
  }

}
<?php

/**
 * @file Star.php
 * @brief This file contains the Star class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Accessory;


use ElephantOnCouch\Doc\Doc;

use PitPress\Model\Storable;


/**
 * @brief This class is used to keep trace of the user favourites.
 * @nosubgrouping
 */
class Star extends Doc {

  /**
   * @brief Creates an instance of Star class.
   */
  public static function create($userId, Storable $item, $timestamp = NULL) {
    $instance = new self();

    $instance->meta["userId"] = $userId;
    $instance->meta["itemId"] = $item->unversionId;
    $instance->meta["itemType"] = $item->type;

    // Articles, questions, books have a unique supertype: post.
    if ($item->isMetadataPresent('supertype'))
      $instance->meta["supertype"] = $item->supertype;

    // A post can be published or not.
    if ($item->isMetadataPresent('itemPublishedAt'))
      $instance->meta["itemPublishedAt"] = $item->publishedAt;

    if (is_null($timestamp))
      $instance->meta["addedAt"] = time();
    else
      $instance->meta["addedAt"] = $timestamp;

    return $instance;
  }

}
<?php

/**
 * @file Star.php
 * @brief This file contains the Star class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Model;


use ReIndex\Feature\Starrable;

use EoC\Doc\Doc;

use ReIndex\Helper\Text;


/**
 * @brief This class is used to keep trace of the user favourites.
 * @nosubgrouping
 */
class Star extends Doc {

  /**
   * @brief Creates an instance of Star class.
   */
  public static function create(Member $member, Starrable $item, $timestamp = NULL) {
    $instance = new self();

    $instance->meta["userId"] = $member->id;
    $instance->meta["itemId"] = Text::unversion($item->getId());
    $instance->meta["itemType"] = $item->getType();
    $instance->meta["itemSupertype"] = $item->getSupertype();;

    // A post can be published or not.
    if ($item->isMetadataPresent('publishedAt'))
      $instance->meta["itemPublishedAt"] = $item->publishedAt;

    if (is_null($timestamp))
      $instance->meta["itemAddedAt"] = time();
    else
      $instance->meta["itemAddedAt"] = $timestamp;

    return $instance;
  }

}
<?php

/**
 * @file Star.php
 * @brief This file contains the Star class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Model;


use EoC\Doc\Doc;

use ReIndex\Helper\Text;


/**
 * @brief This class is used to keep trace of the member's favorites.
 * @nosubgrouping
 */
class Star extends Doc {

  /**
   * @brief Creates an instance of Star class.
   */
  public static function create(Member $member, Post $post, $timestamp = NULL) {
    $instance = new self();

    $instance->meta["userId"] = $member->id;
    $instance->meta["postId"] = Text::unversion($post->getId());
    $instance->meta["postType"] = $post->getType();

    // A post can be published or not.
    if ($post->isMetadataPresent('publishedAt'))
      $instance->meta["postPublishedAt"] = $post->publishedAt;

    if (is_null($timestamp))
      $instance->meta["postAddedAt"] = time();
    else
      $instance->meta["postAddedAt"] = $timestamp;

    return $instance;
  }

}
<?php

/**
 * @file Classification.php
 * @brief This file contains the Classification class.
 * @details
 * @author Filippo F. Fadda
 */


/**
 * @brief PitPress accessory classes namespace.
 */
namespace PitPress\Model\Accessory;


use ElephantOnCouch\Doc\Doc;

use PitPress\Model\Post;


/**
 * @brief This class is used to classify posts.
 * @details Every document belongs from one to five different categories, most commonly known as tags. Each time a post
 * is stored, we must save the associations with its related tags.
 * We can't save the tags' references in the post itself, because we cannot modify the item if one of the associated
 * tags has been deleted, so we must save a new document that stores the association between the post and his tag. To
 * build the relation we must create a so called classification document (an instance of the present class) for each
 * tag related to the post.
 * @nosubgrouping
 */
class Classification extends Doc {

  /**
   * @brief Creates an instance of Classification class.
   */
  public static function create(Post $post, $tagId, $timestamp = NULL) {
    $instance = new self();

    $instance->meta["postId"] = $post->id;
    $instance->meta["postType"] = $post->getType();
    $instance->meta["tagId"] = $tagId;

    if (is_null($timestamp))
      $instance->meta["timestamp"] = time();
    else
      $instance->meta["timestamp"] = $timestamp;

    return $instance;
  }

}
<?php

 * @file Aggregate.php
 * @brief This file contains the Aggregate class.
 * @details
 * @author Filippo F. Fadda


namespace PitPress\Model;


use PitPress\Property;


 * @brief This class represents an aggregate of posts.
 * @nosubgrouping
abstract class Aggregate extends Post {
  use Property\TExcerpt;


  public function __construct() {
    parent::__construct();
    $this->meta['posts'] = [];
  }


   * @name Posts Management Methods
  //@{

   * @brief Removes all posts.
  public function clear() {
    unset($this->meta['posts']);
    $this->meta['posts'] = [];
  }


   * Adds a post to the aggregate.
   * @param[in] string $postId The post id.
   * @param[in] integer $index (optional) Used to order the position of the post inside the aggregate.
  public function addPost($postId, $index = NULL) {
    if (!in_array($postId, $this->meta['posts']))
      if (is_null($index) or $index > (count($this->meta['posts']) - 1))
        $this->meta['posts'][] = $postId;
      else {
        if ($index < 0)
          $index = 0;

        array_splice($this->meta['posts'], $index, 1, $postId);
      }
  }


   * Removes a post from the aggregate.
  public function removePost($postId) {
    $index = array_search($postId, $this->meta[$postId]);

    if ($index != FALSE)
      unset($this->meta['posts'][$index]);
  }


   * Changes the position of the post inside the aggregate.
   * @param[in] string $postId The post id.
   * @param[in] integer $index (optional) Used to order the position of the post inside the aggregate.
  public function movePost($postId, $index = NULL) {
    $this->removePost($postId);
    $this->addPost($postId, $index);
  }

  //@}

}

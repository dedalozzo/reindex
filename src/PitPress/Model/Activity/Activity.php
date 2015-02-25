<?php

/**
 * @file Activity.php
 * @brief This file contains the Activity class.
 * @details
 * @author Filippo F. Fadda
 */


//! This is the activities namespace.
namespace PitPress\Model\Activity;


use EoC\Doc\Doc;


/*
 * @brief todo
 */
abstract class Activity extends Doc {

  protected $badges = []; // Stores the badges' list.


  /**
   * @brief Initializes the badges list.
   */
  abstract public function load();


  /**
   * @brief Notifies all the interested badges to update themselves.
   * @details Activity is the subject while badge is the observer.
   */
  public function notify() {
    foreach ($this->badges as $badge)
      $badge::update();
  }


  /**
   * @brief Adds the specify badge to the list of badges.
   * @param[in] string $badgeClass The complete class name of the badge, including his namespace.
   */
  public function attach($badgeClass) {
    $this->badges[] = $badgeClass;
  }


  // In case of a Post we add the tags, so we can obtain the favorites of a specific tag.
  /*
  if (method_exists($item, 'getTags')) {
    $tags = array_column($item->getTags(), 'id');
    ArrayHelper::unversion($tags);
    $instance->meta['itemTags'] = $tags;
  }
  */

}
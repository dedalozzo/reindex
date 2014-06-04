<?php

/**
 * @file BadgeLoader.php
 * @brief This file contains the BadgeLoader class.
 * @details
 * @author Filippo F. Fadda
 */


//! PitPress loaders namespace.
namespace PitPress\Loader;


use PitPress\Filter\BadgeRecursiveFilterIterator;


/**
 * @brief
 * @details
 * @nosubgrouping
 */
class BadgeLoader {

  protected $folder;


  public function __construct($folder) {
    $this->folder = $folder;
  }


  public function scanForBadges() {
    $dir = new \RecursiveDirectoryIterator($this->folder);
    $filter = new BadgeRecursiveFilterIterator($dir);
    $iterator = new \RecursiveIteratorIterator($filter);

    $badges = [];

    foreach ($iterator as $item) {
      $badges[] = $item;
    }

    return $badges;
  }

} 
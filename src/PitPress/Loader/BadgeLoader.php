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
use PitPress\Helper;


/**
 * @brief
 * @details
 * @nosubgrouping
 */
class BadgeLoader {

  protected $folder;

  protected $badges = [];


  public function __construct($folder) {
    $this->folder = $folder;
  }


  protected function setAwardedCount() {

  }


  public function scanForBadges() {
    $dir = new \RecursiveDirectoryIterator($this->folder);
    $filter = new BadgeRecursiveFilterIterator($dir);
    $iterator = new \RecursiveIteratorIterator($filter);

    foreach ($iterator as $item) {
      $class = Helper\ClassHelper::getClass($item->getPathname());
      $badge = new $class();

      $this->badges[$class]['category'] = basename($item->getPath());
      $this->badges[$class]['name'] = $badge->name;
      $this->badges[$class]['brief'] = $badge->brief;
      $this->badges[$class]['metal'] = $badge->metal;
    }
  }


  // tutti, ottenuti, non ottenuti, oro, argento, bronzo, per tag
  public function getAllBadges() {
    return $this->badges;

    //$this->filterByNamespace();

    //alfabeticOrder

  }


  public function getBronzeBadges() {

  }


  public function getSilverBadges() {

  }


  public function getGoldBadges() {

  }


  public function getEarnedBadges() {

  }


  public function getUnearnedBadges() {

  }


  public function filterByNamespace() {

  }

} 
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
    $this->scanForBadges();
  }


  protected function setAwardedCount() {

  }


  protected function scanForBadges() {
    $dir = new \RecursiveDirectoryIterator($this->folder);
    $filter = new BadgeRecursiveFilterIterator($dir);
    $iterator = new \RecursiveIteratorIterator($filter);

    foreach ($iterator as $item) {
      $class = Helper\ClassHelper::getClass($item->getPathname());
      $badge = new $class();

      $this->badges[$badge->name]['class'] = $class;
      $this->badges[$badge->name]['metal'] = $badge->metal;
      $this->badges[$badge->name]['category'] = basename($item->getPath());
      $this->badges[$badge->name]['brief'] = $badge->brief;
    }

    ksort($this->badges);
  }


  protected function filterBadges($badges, $filterName, $filterValue) {
    $filtered = [];
    foreach ($badges as $key => $value)

      if ($value[$filterName] == $filterValue)
        $filtered[$key] = $value;

    return $filtered;
  }


  /**
   * @brief Returns the list of all badges.
   * @return array An associative array.
   */
  public function getAllBadges() {
    return $this->badges;
  }



  /**
   * @brief Returns the list of badges rewarded to the user.
   * @return array An associative array.
   */
  public function getEarnedBadges() {

  }


  /**
   * @brief Returns the list of badges not rewarded to the user.
   * @return array An associative array.
   */
  public function getUnearnedBadges() {

  }


  /**
   * @brief Returns the list of badges filtered by metal.
   * @param[in] string $metal Specify the metal used for building badges: `gold`, `silver` or `bronze`.
   * @return array An associative array.
   */
  public function filterByMetal($badges, $metal) {
    return $this->filterBadges($badges, 'metal', $metal);
  }


  /**
   * @brief Returns the list of badges filtered by category.
   * @param[in] string $category Specify the category to which the badges belong.
   * @return array An associative array.
   */
  public function filterByCategory($badges, $category) {
    return $this->filterBadges($badges, 'category', $category);
  }

} 
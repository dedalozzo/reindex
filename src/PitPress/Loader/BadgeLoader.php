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

use EoC\Opt\ViewQueryOpts;


/**
 * @brief
 * @details
 * @nosubgrouping
 */
class BadgeLoader {
  protected $guardian;
  protected $couch;
  protected $log;
  protected $user;
  protected $folder;
  protected $badges = [];


  public function __construct($di, $folder) {
    $this->guardian = $di['guardian'];
    $this->couch = $di['couchdb'];
    $this->log = $di['log'];
    $this->user = $this->guardian->getUser();
    $this->folder = $folder;
    $this->scanForBadges();
  }


  private function setAwardedCount($forUser = FALSE) {
    $opts = new ViewQueryOpts();
    $opts->reset();
    $opts->includeMissingKeys()->groupResults();

    $classes = array_column($this->badges, 'class');

    if ($forUser) {
      $field = 'earned';

      $keys = [];
      foreach ($classes as $class)
        $keys[] = [$class, $this->user->id];

      $result = $this->couch->queryView("badges", "perClassAndUser", $keys, $opts);
    }
    else {
      $field = 'awarded';

      $result = $this->couch->queryView("badges", "perClass", $classes, $opts);
    }

    $badgesCount = count($result);
    for ($i = 0; $i < $badgesCount; $i++)
      $this->badges[$i][$field] = is_null($result[$i]['value']) ? 0 : $result[$i]['value'];
  }


  private function setEarnedCount() {
    if (!$this->user->isGuest())
      $this->setAwardedCount(TRUE);
  }


  protected function scanForBadges() {
    $dir = new \RecursiveDirectoryIterator($this->folder);
    $filter = new BadgeRecursiveFilterIterator($dir);
    $iterator = new \RecursiveIteratorIterator($filter);

    $i = 0;
    foreach ($iterator as $fileInfo) {
      $class = Helper\ClassHelper::getClass($fileInfo->getPathname());
      $badge = new $class();

      $this->badges[$i]['class'] = $class;
      $this->badges[$i]['name'] = $badge->name;
      $this->badges[$i]['metal'] = $badge->metal;
      $this->badges[$i]['messages'] = $badge->messages;
      $this->badges[$i]['category'] = basename($fileInfo->getPath());
      $this->badges[$i]['brief'] = $badge->brief;
      $this->badges[$i]['awarded'] = 0;
      $this->badges[$i]['earned'] = 0;

      $i++;
    }

    $this->sortBadges($this->badges, 'name');

    $this->setAwardedCount();
    $this->setEarnedCount();
  }


  protected function sortBadges(&$badges, $field) {
    $func = function($a, $b) use ($field) {
      return strcmp($a[$field], $b[$field]);
    };

    usort($badges, $func);
  }


  protected function filterBadges($badges, $filterName, $filterValue) {
    $filtered = [];
    foreach ($badges as $badge)

      if ($badge[$filterName] == $filterValue)
        $filtered[] = $badge;

    return $filtered;
  }


  /**
   * @brief Returns the list of all badges.
   * @retval array An associative array.
   */
  public function getAllBadges() {
    return $this->badges;
  }


  /**
   * @brief Returns the list of badges rewarded to the user.
   * @retval array An associative array.
   */
  public function getEarnedBadges() {
    $func = function($value) {
      if ($value['earned'] > 0)
        return TRUE;
      else
        return FALSE;
    };

    return array_filter($this->badges, $func);
  }


  /**
   * @brief Returns the list of badges not rewarded to the user.
   * @retval array An associative array.
   */
  public function getUnearnedBadges() {
    $func = function($value) {
      if ($value['earned'] === 0)
        return TRUE;
      else
        return FALSE;
    };

    return array_filter($this->badges, $func);
  }


  /**
   * @brief Returns the list of badges filtered by metal.
   * @param[in] string $metal Specify the metal used for building badges: `gold`, `silver` or `bronze`.
   * @retval array An associative array.
   */
  public function filterByMetal($badges, $metal) {
    return $this->filterBadges($badges, 'metal', $metal);
  }


  /**
   * @brief Returns the list of badges filtered by category.
   * @param[in] string $category Specify the category to which the badges belong.
   * @retval array An associative array.
   */
  public function filterByCategory($badges, $category) {
    return $this->filterBadges($badges, 'category', $category);
  }

} 
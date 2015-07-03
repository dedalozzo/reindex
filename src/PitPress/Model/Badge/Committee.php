<?php

/**
 * @file Committee.php
 * @brief This file contains the Committee class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge;


use PitPress\Filter\BadgeRecursiveFilterIterator;
use PitPress\Helper;
use PitPress\Model\Badge\Decorator\Decorator;

use EoC\Opt\ViewQueryOpts;


/**
 * @brief The committee awards or withdrawn badges, lists them, and provides information about decorators.
 * @details
 * @nosubgrouping
 */
class Committee {
  protected $guardian;
  protected $couch;
  protected $log;
  protected $user;
  protected $folder;
  protected $decorators = [];


  public function __construct($di, $folder) {
    $this->guardian = $di['guardian'];
    $this->couch = $di['couchdb'];
    $this->log = $di['log'];
    $this->user = $this->guardian->getUser();
    $this->folder = $folder;
    $this->scan();
  }


  private function setAwardedCount($forUser = FALSE) {
    $opts = new ViewQueryOpts();
    $opts->reset();
    $opts->includeMissingKeys()->groupResults();

    $classes = array_column($this->decorators, 'class');

    if ($forUser) {
      $field = 'earned';

      $keys = [];
      foreach ($classes as $class)
        $keys[] = [$class, $this->user->id];

      $result = $this->couch->queryView("badges", "perDecoratorAndUser", $keys, $opts);
    }
    else {
      $field = 'awarded';

      $result = $this->couch->queryView("badges", "perDecorator", $classes, $opts);
    }

    $badgesCount = count($result);
    for ($i = 0; $i < $badgesCount; $i++)
      $this->decorators[$i][$field] = is_null($result[$i]['value']) ? 0 : $result[$i]['value'];
  }


  private function setEarnedCount() {
    if (!$this->user->isGuest())
      $this->setAwardedCount(TRUE);
  }


  protected function scan() {
    $dir = new \RecursiveDirectoryIterator($this->folder);
    $filter = new BadgeRecursiveFilterIterator($dir);
    $iterator = new \RecursiveIteratorIterator($filter);

    $i = 0;
    foreach ($iterator as $fileInfo) {
      $class = Helper\ClassHelper::getClass($fileInfo->getPathname());
      $decorator = $this->newDecorator($class);

      $this->decorators[$i]['class'] = $class;
      $this->decorators[$i]['name'] = $decorator->name;
      $this->decorators[$i]['metal'] = $decorator->metal;
      $this->decorators[$i]['messages'] = $decorator->messages;
      $this->decorators[$i]['category'] = basename($fileInfo->getPath());
      $this->decorators[$i]['brief'] = $decorator->brief;
      $this->decorators[$i]['awarded'] = 0;
      $this->decorators[$i]['earned'] = 0;

      $i++;
    }

    $this->sort($this->decorators, 'name');

    $this->setAwardedCount();
    $this->setEarnedCount();
  }


  protected function sort(&$decorators, $field) {
    $func = function($a, $b) use ($field) {
      return strcmp($a[$field], $b[$field]);
    };

    usort($decorators, $func);
  }


  protected function filter($filterName, $filterValue) {
    $filtered = [];
    foreach ($this->decorators as $decorator)

      if ($decorator[$filterName] == $filterValue)
        $filtered[] = $decorator;

    return $filtered;
  }


  public function newDecorator($class) {
    return new $class($this);
  }


  /**
   * @brief Awards a badge of the provided class to the user for the specified tag, if any.
   */
  public function awardBadge(Decorator $decorator, $userId, $tagId) {
    //$class = get_class($decorator);
  }


  /**
   * @brief Withdrawn a badge of the provided class previously awarded to the user for the specified tag, if any.
   * @details Only some badges might be retired, but this is something the decorator itself can know.
   * @param[in] Decorator $decorator The badge's decorator.
   * @param[in] string
   */
  public function withdrawnBadge(Decorator $decorator, $userId, $tagId) {
    //$class = get_class($decorator);
  }



  /**
   * @brief Returns the list of all decorators.
   * @retval array An associative array.
   */
  public function getDecorators() {
    return $this->decorators;
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

    return array_filter($this->decorators, $func);
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

    return array_filter($this->decorators, $func);
  }


  /**
   * @brief Returns the list of badges filtered by metal.
   * @param[in] string $metal Specify the metal used for building badges: `gold`, `silver` or `bronze`.
   * @retval array An associative array.
   */
  public function filterByMetal($metal) {
    return $this->filter('metal', $metal);
  }


  /**
   * @brief Returns the list of badges filtered by category.
   * @param[in] string $category Specify the category to which the badges belong.
   * @retval array An associative array.
   */
  public function filterByCategory($category) {
    return $this->filter('category', $category);
  }

} 
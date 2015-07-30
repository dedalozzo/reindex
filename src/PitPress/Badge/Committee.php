<?php

/**
 * @file Committee.php
 * @brief This file contains the Committee class.
 * @details
 * @author Filippo F. Fadda
 */


//! PitPress badge system namespace.
namespace PitPress\Badge;


use PitPress\Filter\DecoratorRecursiveFilterIterator;
use PitPress\Helper;
use PitPress\Badge\Decorator\Decorator;

use EoC\Opt\ViewQueryOpts;


/**
 * @brief The committee awards or withdrawns badges, lists them, and provides information about decorators.
 * @details
 * @nosubgrouping
 */
class Committee {
  protected $guardian;
  protected $changeManager;
  protected $couch;
  protected $log;
  protected $user;
  protected $folder;
  protected $decorators = [];


  /**
   * @brief Constructor.
   * @param[in] DependencyInjector $di Phalcon dependency injector.
   * @param[in] string $folder Decorators' directory.
   */
  public function __construct($di, $folder) {
    $this->guardian = $di['guardian'];
    $this->couch = $di['couchdb'];
    $this->log = $di['log'];
    $this->user = $this->guardian->getUser();
    $this->folder = $folder;
    $this->scan();

    if (isset($di['changeManager'])) {
      $this->changeManager = $di['changeManager'];

      foreach ($this->decorators as $decoratorInfo) {
        $decorator = $decoratorInfo['instance'];
        $this->changeManager->register($decorator);
      }
    }

  }


  /**
   * @brief Computes for each badge, the number of times has been awarded.
   * @param[in] bool $forUser Restricts the count to the current user.
   */
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


  /**
   * @brief Computes for each badge, the number of times has been awarded to the current user.
   */
  private function setEarnedCount() {
    if (!$this->user->isGuest())
      $this->setAwardedCount(TRUE);
  }


  /**
   * @brief Scans the directory searching for decorators.
   */
  protected function scan() {
    $dir = new \RecursiveDirectoryIterator($this->folder);
    $filter = new DecoratorRecursiveFilterIterator($dir);
    $iterator = new \RecursiveIteratorIterator($filter);

    $i = 0;
    foreach ($iterator as $fileInfo) {
      $class = Helper\ClassHelper::getClass($fileInfo->getPathname());
      $decorator = $this->newDecorator($class);

      $this->decorators[$i]['instance'] = $decorator;
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


  /**
   * @brief Sorts the list of decorators.
   * @param[in] array $decorators The list of decorators.
   * @param[in] string $field Sorts using this field.
   * @return array The filtered list.
   */
  protected function sort(&$decorators, $field) {
    $func = function($a, $b) use ($field) {
      return strcmp($a[$field], $b[$field]);
    };

    usort($decorators, $func);
  }


  /**
   * @brief Filters the list of decorators using a filter.
   * @param[in] string $filterName The filter's name.
   * @param[in] string $filterName The filter's value.
   * @return array An associative array.
   */
  protected function filter($filterName, $filterValue) {
    $filtered = [];
    foreach ($this->decorators as $decorator)

      if ($decorator[$filterName] === $filterValue)
        $filtered[] = $decorator;

    return $filtered;
  }


  /**
   * @brief Returns an instance of the provided decorator class.
   * @param[in] string $class A decorator's class.
   * @return object
   */
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
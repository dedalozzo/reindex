<?php

/**
 * @file FakeCollection.php
 * @brief This file contains the FakeCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Model\Member;
use EoC\Couch;

use Phalcon\Di;


/**
 * @brief This class is used to represent a collection who doesn't store any real data but relay instead on a database.
 * @details This class implements `Countable`.
 * @nosubgrouping
 */
abstract class FakeCollection implements \Countable {

  /**
   * @var Di $di
   */
  protected $di;

  /**
   * @var Couch $couch
   */
  protected $couch;

  /**
   * @var \Redis $redis
   */
  protected $redis;

  /**
   * @var Member $user
   */
  protected $user;


  /**
   * @brief Creates a new collection of items.
   */
  public function __construct(Member $user) {
    $this->user = $user;
    $this->di = Di::getDefault();
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];
  }


  /**
   * @brief This method returns the collection count.
   */
  abstract protected function getCount();


  /**
   * @brief Returns the number of collection's items.
   * @retval integer
   */
  public function count() {
    return $this->getCount();
  }


  /**
   * @brief Returns `true` in case there aren't items inside the collection, `false` otherwise.
   * @attention This method must be used in place of `empty()`.
   * @retval bool
   */
  public function isEmpty() {
    return empty($this->getCount()) ? TRUE : FALSE;
  }

}
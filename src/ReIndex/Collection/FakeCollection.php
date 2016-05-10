<?php

/**
 * @file FakeCollection.php
 * @brief This file contains the FakeCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Model\Member;

use Phalcon\Di;


/**
 * @brief This class is used to represent a collection who doesn't store any real data but relay instead on a database.
 * @details This class implements `Countable`.
 * @nosubgrouping
 */
abstract class FakeCollection implements \Countable {

  protected $di;    // Stores the default Dependency Injector.
  protected $user;  // Stores the current user.
  protected $couch; // Stores the CouchDB instance.
  protected $redis; // Stores the Redis instance.


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
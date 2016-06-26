<?php

/**
 * @file MemberCollection.php
 * @brief This file contains the MemberCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Doc\Member;
use EoC\Couch;

use Phalcon\Di;


/**
 * @brief This class is used to represent a collection of members.
 * @details This class is abstract, cannot be instantiated. It doesn't store any real data but relay instead on a database.
 * @details This class implements `Countable`.
 * @nosubgrouping
 */
abstract class MemberCollection implements \Countable {

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
   * @var Member $member
   */
  protected $member;


  /**
   * @brief Creates a new collection of items.
   * @param[in] Member $member
   */
  public function __construct(Member $member) {
    $this->member = $member;
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
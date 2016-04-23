<?php

/**
 * @file Blacklist.php
 * @brief This file contains the Blacklist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Model\Member;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;


/**
 * @brief This class is used to represent the member's blacklist.
 * @details This class uses the Lazy loading pattern.
 */
class Blacklist extends AbstractCollection {

  const NAME = "blacklist";

  protected $couch; // Stores the CouchDB instance.

  protected $blacklist = NULL; // Stores the blacklist.


  /**
   * @brief Creates a new collection of e-mails.
   * @param[in] array $meta Member's array of metadata.
   */
  public function __construct(array &$meta) {
    parent::__construct($meta);
    $this->couch = $this->di['couchdb'];
  }


  /**
   * @brief Using the lady loading pattern, this method returns the member's blacklist.
   * @details Since the members data resides on a database, the system prevent from loading them, unless they are
   * strictly needed.
   * @attention The blacklist is not sorted by username or full name.
   */
  protected function getBlacklist() {
    // Test is made using `is_null()` instead of `empty()` because a member may not have a blacklist.
    if (is_null($this->blacklist)) {
      $opts = new ViewQueryOpts();
      $opts->doNotReduce();

      // Assigns the members' IDs.
      $ids = array_keys($this->meta[static::NAME]);

      $this->data = $this->couch->queryView("members", "all", $ids, $opts)->asArray();
    }

    return $this->blacklist;
  }


  /**
   * @brief Adds a member to the blacklist.
   * @param[in] Member $member A member.
   */
  public function add(Member $member) {
    // Stores just the member ID.
    $this->meta[static::NAME][$member->id];
  }


  /**
   * @brief Removes the specified member from the blacklist.
   * @param[in] Member $member A member.
   */
  public function remove(Member $member) {
    unset($this->meta[static::NAME][$member->id]);
  }


  /**
   * @brief Returns `true` if the member has been blacklisted, `false` otherwise.
   * @param[in] Member $member A member.
   * @retval bool
   */
  public function exists(Member $member) {
    return array_key_exists($this->meta[static::NAME], $member->id);
  }


  /**
   * @brief Returns the collection as a real array.
   */
  public function asArray() {
    return $this->getBlacklist()->asArray();
  }

}
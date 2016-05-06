<?php

/**
 * @file FollowerCollection.php
 * @brief This file contains the FollowerCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Model\Member;
use ReIndex\Exception;
use ReIndex\Model\Follower;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use Phalcon\Di;


/**
 * @brief This class is used to represent a collection of followers.
 */
class FollowerCollection extends FakeCollection {

  protected $followersCount = NULL; // Stores the number of followers in the collection.


  /**
   * @brief Using the lady loading pattern, this method returns the member's friendships count.
   * @details Since the friends data resides on a database, the system prevent from loading them, unless they are
   * strictly needed.
   */
  protected function getCount() {
    // Test is made using `is_null()` instead of `empty()` because a member may have no followers at all.
    if (is_null($this->followersCount)) {
      $opts = new ViewQueryOpts();
      $opts->reduce()->setKey([$this->user->id]);

      $this->followersCount = $this->couch->queryView("followers", "perMember", NULL, $opts)->getReducedValue();
    }

    return $this->followersCount;
  }


  /**
   * @brief Follows the specified member.
   * @param[in] Member $member A member.
   * @retval bool Returns `true` in case of success, `false` otherwise.
   */
  public function follow(Member $member) {
    // Of course, you can't add yourself to the collection.
    if ($member->match($this->user->id))
      throw new Exception\UserMismatchException("You are nut.");

    // You are already following the member.
    if ($this->exists($member))
      throw new Exception\UserMismatchException("You are already following him.");

    // Creates and stores the relation.
    $follower = Follower::request($this->user, $member);
    $follower->save();
  }


  /**
   * @brief Unfollows the specified member.
   * @param[in] Member $member A member.
   * @retval bool Returns `true` in case of success, `false` otherwise.
   */
  public function unfollow(Member $member) {
    if ($follower = $this->exists($member)) {
      $follower->delete();
      $follower->save();
    }
    else
      throw new Exception\UserMismatchException("You are not following him.");
  }


  /**
   * @brief Returns `true` in case the current user is following the specified member, `false` otherwise.
   * @param[in] Member $member A member.
   * @retval Model::Follower or `false` in case the member is not a follower.
   */
  public function exists(Member $member) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([$this->user->id, $member->id]);

    $result = $this->couch->queryView("followers", "perMember", NULL, $opts);

    if ($result->isEmpty())
      return FALSE;
    else
      return $this->couch->getDoc(Couch::STD_DOC_PATH, $result[0]['id']);
  }

}
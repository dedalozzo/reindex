<?php

/**
 * @file FollowerCollection.php
 * @brief This file contains the FollowerCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Doc\Member;
use ReIndex\Exception;
use ReIndex\Doc\Follower;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;


/**
 * @brief This class is used to represent a collection of followers.
 * @nosubgrouping
 */
final class FollowerCollection extends MemberCollection {

  protected $followersCount = NULL; // Stores the number of followers in the collection.


  protected function getCount() {
    // Test is made using `is_null()` instead of `empty()` because a member may have no followers at all.
    if (is_null($this->followersCount)) {
      $opts = new ViewQueryOpts();
      $opts->reduce()->setKey([$this->member->id]);

      //followers/perMember/view
      $this->followersCount = $this->couch->queryView('followers', 'perMember', 'view', NULL, $opts)->getReducedValue();
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
    if ($member->match($this->member->id))
      throw new Exception\UserMismatchException("You are nut.");

    // You are already following the member.
    if ($member->followers->exists($this->member))
      throw new Exception\UserMismatchException("You are already following him.");

    // Creates and stores the relation.
    $follower = Follower::create($member->id, $this->member->id);
    $this->couch->save($follower);
  }


  /**
   * @brief Unfollows the specified member.
   * @param[in] Member $member A member.
   * @retval bool Returns `true` in case of success, `false` otherwise.
   */
  public function unfollow(Member $member) {
    if ($follower = $member->followers->exists($this->member))
      $this->couch->deleteDoc(Couch::STD_DOC_PATH, $follower->id, $follower->rev);
    else
      throw new Exception\UserMismatchException("You are not following him.");
  }


  /**
   * @brief Returns `true` in case the current user is following the specified member, `false` otherwise.
   * @param[in] Member $member A member.
   * @retval Doc::Follower or `false` in case the member is not a follower.
   */
  public function exists(Member $member) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([$this->member->id, $member->id]);

    //followers/perMember/view
    $result = $this->couch->queryView('followers', 'perMember', 'view', NULL, $opts);

    if ($result->isEmpty())
      return FALSE;
    else
      return $this->couch->getDoc(Couch::STD_DOC_PATH, $result[0]['id']);
  }

}
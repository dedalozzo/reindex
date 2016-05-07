<?php

/**
 * @file FriendCollection.php
 * @brief This file contains the FriendCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Model\Member;
use ReIndex\Exception;
use ReIndex\Model\Friendship;
use ReIndex\Model\Follower;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use Phalcon\Di;


/**
 * @brief This class is used to represent a collection of friends.
 * @details This class implements `IteratorAggregate`, `Countable`, and `ArrayAccess`.
 * This class uses the Lazy loading pattern.
 * @nosubgrouping
 */
class FriendCollection extends FakeCollection {

  protected $friendsCount = NULL; // Stores the number of friends in the collection.


  protected function getCount() {
    // Test is made using `is_null()` instead of `empty()` because a member may have no friends at all.
    if (is_null($this->friendsCount)) {
      $opts = new ViewQueryOpts();
      $opts->doNotReduce()->setKey([$this->user->id]);

      $this->friendsCount = $this->couch->queryView("friendship", "perMember", NULL, $opts)->getReducedValu();
    }

    return $this->friendsCount;
  }


  /**
   * @brief Adds the specified member to the friends collection.
   * @details Every new friendship relation must be approved.
   * @param[in] Member $member A member.
   */
  public function add(Member $member) {
    // Of course, you can't add yourself to the collection.
    if ($member->match($this->user->id))
      throw new Exception\UserMismatchException("You are nut.");

    if ($member->blacklist->exists($this->user))
      throw new Exception\UserMismatchException("Unfortunately you have been blacklisted from the user you are trying to add as a friend.");

    if ($this->exists($member))
      throw new Exception\UserMismatchException("You are already friend with the user.");

    if ($this->pendingRequest($member))
      throw new Exception\UserMismatchException("You have already sent a friend request to this user.");

    // Creates and stores the friendship.
    $friendship = Friendship::request($this->user->id, $member->id);
    $this->couch->saveDoc($friendship);

    // Follows the member.
    if (!$member->followers->exists($this->user)) {
      $follower = Follower::create($member->id, $this->user->id);
      $this->couch->saveDoc($follower);
    }
  }


  /**
   * @brief Removes the specified member from your friendships.
   * @details The algorithm doesn't care if the friendship has been approved or not, it just removes it.
   * @param[in] Member $member A member.
   */
  public function remove(Member $member) {
    if ($friendship = $this->exists($member)) {
      $this->couch->deleteDoc(Couch::STD_DOC_PATH, $friendship->id, $friendship->rev);

      // If you remove the member from your friends, you don't follow it anymore.
      if ($follower = $member->followers->exists($this->user))
        $this->couch->deleteDoc(Couch::STD_DOC_PATH, $follower->id, $follower->rev);
    }
    else
      throw new Exception\UserMismatchException("You are not friends.");
  }


  /**
   * @brief Returns `true` if there is an established friendship relation with the specified member, `false` otherwise.
   * @param[in] Member $member A member.
   * @retval Model::Friendship or `false` in case the member is not a friend.
   */
  public function exists(Member $member) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([$this->user->id, $member->id]);

    $result = $this->couch->queryView("friendships", "approvedPerMember", NULL, $opts);

    if ($result->isEmpty())
      return FALSE;
    else
      return $this->couch->getDoc(Couch::STD_DOC_PATH, $result[0]['id']);
  }


  /**
   * @brief Returns a friendship object if a friendship request exists, `false` otherwise.
   * @param[in] Member $member A member.
   * @retval Model::Friendship
   */
  public function pendingRequest(Member $member) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([$member->id, $this->user->id]);

    $result = $this->couch->queryView("friendships", "pendingRequest", NULL, $opts);

    if ($result->isEmpty())
      return FALSE;
    else
      return $this->couch->getDoc(Couch::STD_DOC_PATH, $result[0]['id']);
  }


  /**
   * @brief Approves the friendship with the specified member.
   * @param[in] Member $member A member.
   */
  public function approve(Member $member) {
    if ($friendship = $this->pendingRequest($member)) {

      if (!$this->user->match($friendship->receiverId))
        throw new Exception\UserMismatchException("It's not up to you approve someone else's friendship.");

      $friendship->approve();
      $this->couch->saveDoc($friendship);

      // Follows the member.
      if (!$member->followers->exists($this->user)) {
        $follower = Follower::create($member->id, $this->user->id);
        $this->couch->saveDoc($follower);
      }
    }
    else
      throw new Exception\UserMismatchException("The friendship request doesn't exist anymore.");
  }


  /**
   * @brief Rejects the friendship with the specified member.
   * @param[in] Member $member A member.
   * @todo Send the reject notification.
   */
  public function reject(Member $member) {
    if ($friendship = $this->pendingRequest($member)) {

      if (!$this->user->match($friendship->receiverId))
        throw new Exception\UserMismatchException("It's not up to you reject someone else's friendship.");

      $this->couch->deleteDoc(Couch::STD_DOC_PATH, $friendship->id, $friendship->rev);
    }
    else
      throw new Exception\UserMismatchException("The friendship request doesn't exist anymore.");
  }


  /**
   * @brief Adds a bunch of potential friends to the friends collection, using a list of e-mails.
   * @todo Implement the inviteFriends() method.
   */
  public function invite() {
  }
  
}
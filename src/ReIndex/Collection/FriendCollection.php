<?php

/**
 * @file FriendCollection.php
 * @brief This file contains the FriendCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Exception;
use ReIndex\Doc\Member;
use ReIndex\Doc\Friendship;
use ReIndex\Doc\Follower;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use Phalcon\Di;


/**
 * @brief This class is used to represent a collection of friends.
 * @nosubgrouping
 */
class FriendCollection extends MemberCollection {

  /** @name Redis Names */
  //!@{

  const TS_SET = '_ts';    //!< Friendship approval timestamp Redis set.
  const UN_SET = '_un';    //!< Username Redis set.
  const FN_SET = '_fn';    //!< Full name Redis set.
  const IFN_SET = '_ifn';  //!< Inverted full name Redis set.

  //!@}


  /**
   * @brief Adds the friendship to the indexes.
   * @attention This function must be called twice, inverting the IDs.
   * @param[in] string $memberId Member ID.
   * @param[in] string $friendId Friend ID.
   */
  protected function indexFrienship($memberId, $friendId) {
    $hash = $this->redis->hMGet($friendId . Member::MR_HASH, ['username', 'fullName', 'invFullName']);

    $this->redis->zAdd($memberId . self::TS_SET, time(), $friendId);
    $this->redis->zAdd($memberId . self::UN_SET, 0, $hash['username'].':'.$friendId);
    $this->redis->zAdd($memberId . self::FN_SET, 0, $hash['fullName'].':'.$friendId);
    $this->redis->zAdd($memberId . self::IFN_SET, 0, $hash['invFullName'].':'.$friendId);
  }


  /**
   * @brief Removes the friendship from the indexes.
   * @attention This function must be called twice, inverting the IDs.
   * @param[in] string $memberId Member ID.
   * @param[in] string $friendId Friend ID.
   */
  protected function deindexFriendship($memberId, $friendId) {
    $hash = $this->redis->hMGet($friendId . Member::MR_HASH, ['username', 'fullName', 'invFullName']);

    $this->redis->zRem($memberId . self::TS_SET, $friendId);
    $this->redis->zRem($memberId . self::UN_SET, $hash['username'].':'.$friendId);
    $this->redis->zRem($memberId . self::FN_SET, $hash['fullName'].':'.$friendId);
    $this->redis->zRem($memberId . self::IFN_SET, $hash['invFullName'].':'.$friendId);
  }


  protected function getCount() {
    return $this->redis->zCount($this->member->id . self::TS_SET, 0, '+inf');
  }


  /**
   * @brief Adds the specified member to the friends collection.
   * @details Every new friendship relation must be approved.
   * @param[in] Member $member A member.
   */
  public function add(Member $member) {
    // Of course, you can't add yourself to the collection.
    if ($member->match($this->member->id))
      throw new Exception\UserMismatchException("You are nut.");

    if ($member->blacklist->exists($this->member))
      throw new Exception\UserMismatchException("Unfortunately you have been blacklisted from the user you are trying to add as a friend.");

    if ($this->exists($member))
      throw new Exception\UserMismatchException("You are already friend with the user.");

    if ($this->pendingRequest($member))
      throw new Exception\UserMismatchException("You have already sent a friend request to this user.");

    // Creates and stores the friendship.
    $friendship = Friendship::request($this->member->id, $member->id);
    $this->couch->saveDoc($friendship);

    // Follows the member.
    if (!$member->followers->exists($this->member)) {
      $follower = Follower::create($member->id, $this->member->id);
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

      $this->redis->multi();

      $this->deindexFriendship($this->member->id, $member->id);
      $this->deindexFriendship($member->id, $this->member->id);

      $this->redis->exec();

      // If you remove the member from your friends, you don't follow it anymore.
      if ($follower = $member->followers->exists($this->member))
        $this->couch->deleteDoc(Couch::STD_DOC_PATH, $follower->id, $follower->rev);
    }
    else
      throw new Exception\UserMismatchException("You are not friends.");
  }


  /**
   * @brief Returns `true` if there is an established friendship relation with the specified member, `false` otherwise.
   * @param[in] Member $member A member.
   * @retval Doc::Friendship or `false` in case the member is not a friend.
   */
  public function exists(Member $member) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([$this->member->id, $member->id]);

    $result = $this->couch->queryView("friendships", "approvedPerMember", NULL, $opts);

    if ($result->isEmpty())
      return FALSE;
    else
      return $this->couch->getDoc(Couch::STD_DOC_PATH, $result[0]['id']);
  }


  /**
   * @brief Returns a friendship object if a friendship request exists, `false` otherwise.
   * @param[in] Member $member A member.
   * @retval Doc::Friendship
   */
  public function pendingRequest(Member $member) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([$member->id, $this->member->id]);

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

      if (!$this->member->match($friendship->receiverId))
        throw new Exception\UserMismatchException("It's not up to you approve someone else's friendship.");

      $friendship->approve();
      $this->couch->saveDoc($friendship);

      $this->redis->multi();

      $this->indexFrienship($this->member->id, $member->id);
      $this->indexFrienship($member->id, $this->member->id);

      $this->redis->exec();

      // Follows the member.
      if (!$member->followers->exists($this->member)) {
        $follower = Follower::create($member->id, $this->member->id);
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

      if (!$this->member->match($friendship->receiverId))
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
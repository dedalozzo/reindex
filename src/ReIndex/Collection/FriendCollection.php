<?php

/**
 * @file FriendCollection.php
 * @brief This file contains the ${CLASS_NAME} class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Model\Member;
use ReIndex\Exception;


class FriendCollection extends AbstractCollection {

  const NAME = "friends";


  public function exists(Member $member, &$friendshipId = NULL, &$approved = FALSE) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([$this->id, $member->id]);

    $result = $this->couch->queryView("friendship", "perMember", NULL, $opts);

    if ($result->isEmpty())
      return FALSE;
    else {
      $friendshipId = $result[0]['id'];
      $approved = $result[0]['approved'];
      return TRUE;
    }
  }


  public function add(Member $member) {
    if ($this->user->match($member->id))
      return;

    if ($this->isBlacklisted($member))
      throw new Exception\UserMismatchException("Unfortunately you have been blacklisted from the user you are trying to add as a friend.");

    if ($this->exists($member, $friendshipId, $approved)) {
      if ($approved)
        throw new Exception\UserMismatchException("You are already friend with the user.");
      else
        throw new Exception\UserMismatchException("You have already sent a friend request to this user.");
    }

    // Creates and stores the friendship.
    $friendship = Friendship::request($member);
    $this->couch->saveDoc($friendship);
  }


  public function remove(Member $member) {
    // We don't care if the friendship has been approved or not, we just remove it.
    if ($this->exists($member, $friendshipId)) {
      $friendship = $this->couch->getDoc(Couch::STD_DOC_PATH, $friendshipId);
      $this->couch->deleteDoc(Couch::STD_DOC_PATH, $friendshipId, $friendship->rev);
    }
    else
      throw new Exception\UserMismatchException("You are not friends.");
  }


  public function approve(Member $member) {
    if ($this->exists($member, $friendshipId)) {
      $friendship = $this->couch->getDoc(Couch::STD_DOC_PATH, $friendshipId);

      if (!$this->match($friendship->receiverId)) throw new Exception\UserMismatchException("You cannot approve someone else's friendship.");

      if (!$friendship->isApproved()) {
        $friendship->approve();
        $this->couch->saveDoc($friendship);
      }
      else
        throw new \RuntimeException("The friendship has been approved already.");
    }
    else
      throw new Exception\UserMismatchException("You are not friends.");
  }


  public function reject(Member $member, $blacklist = FALSE) {

    if ($this->exists($member, $friendshipId)) {
      $friendship = $this->couch->getDoc(Couch::STD_DOC_PATH, $friendshipId);

      if (!$this->match($friendship->receiverId)) throw new Exception\UserMismatchException("It's not up to you approve someone else's friendship.");

      if (!$friendship->isApproved()) {
        $friendship->approve();
        $this->couch->saveDoc($friendship);
      }
      else
        throw new \RuntimeException("The friendship has been approved already.");
    }
    else
      throw new Exception\UserMismatchException("You are not friends.");
  }


  /**
   * @brief Adds a bunch of potential friends to the list of friends, using a list of e-mails.
   * @todo Implement the inviteFriends() method.
   */
  public function invite() {
  }
  
  
}
<?php

/**
 * @file FriendList.php
 * @brief This file contains the FriendList class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Model\Member;
use ReIndex\Exception;
use ReIndex\Model\Friendship;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use Phalcon\Di;


/**
 * @brief This class is used to represent a collection of friends.
 * @details This class uses the Lazy loading pattern.
 */
class FriendList implements \IteratorAggregate, \Countable, \ArrayAccess {

  protected $di;    // Stores the default Dependency Injector.
  protected $user;  // Stores the current user.
  protected $couch; // Stores the CouchDB instance.

  protected $friendships = NULL; // Stores the member's friendships.


  /**
   * @brief Creates a new collection of friends.
   */
  public function __construct() {
    $this->di = Di::getDefault();
    $this->couchdb = $this->di['couchdb'];
    $this->user = $this->di['guardian']->getUser();
  }


  /**
   * @brief Using the lady loading pattern, this method returns the member's friendships.
   * @details Since the friends data resides on a database, the system prevent from loading them, unless they are
   * strictly needed.
   */
  protected function getFriendships() {
    // Test is made using `is_null()` instead of `empty()` because a member may have no friends at all.
    if (is_null($this->data)) {
      $opts = new ViewQueryOpts();
      $opts->doNotReduce()->setKey([$this->id]);

      $this->data = $this->couch->queryView("friendship", "perMember", NULL, $opts)->asArray();
    }

    return $this->data;
  }


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


  /**
   * @brief Adds the specified member to the collection.
   * @details Every new friendship must be approved.
   * @param[in] Member $member A member.
   */
  public function add(Member $member) {
    // Of course, you can't add yourself to the collection.
    if ($member->match($this->user->id))
      return;

    if ($member->blacklist->exists($this->user))
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


  /**
   * @brief Returns the collection as a real array.
   * @retval array An associative array using as keys the e-mail addresses, and as values if the address are verified or
   * not.
   */
  public function asArray() {
    return $this->getFriendships();
  }


  /**
   * @brief Returns an external iterator.
   * @retval [ArrayIterator](http://php.net/manual/en/class.arrayiterator.php).
   */
  public function getIterator() {
    return new \ArrayIterator($this->getFriendships());
  }


  /**
   * @brief Returns the number of member's friendships.
   * @retval integer
   */
  public function count() {
    return count($this->getFriendships());
  }


  /**
   * @brief Returns `true` in case there aren't items inside the collection, `false` otherwise.
   * @details Since the PHP core developers are noobs, `empty()` cannot be used on any class that implements ArrayAccess.
   * @attention This method must be used in place of `empty()`.
   * @retval bool
   */
  public function isEmpty() {
    return empty($this->getFriendships()) ? TRUE : FALSE;
  }


  /**
   * @brief Whether or not an offset exists.
   * @details This method is executed when using `isset()` or `empty()` on objects implementing ArrayAccess.
   * @param[in] integer $offset An offset to check for.
   * @retval bool Returns `true` on success or `false` on failure.
   */
  public function offsetExists($offset) {
    return isset($this->getFriendships()[$offset]);
  }


  /**
   * @brief Returns the value at specified offset.
   * @details This method is executed when checking if offset is `empty()`.
   * @param[in] integer $offset The offset to retrieve.
   * @retval mixed Can return all value types.
   */
  public function offsetGet($offset)  {
    return $this->getFriendships()[$offset];
  }


  //! @cond HIDDEN_SYMBOLS

  public function offsetSet($offset, $value) {
    throw new \BadMethodCallException("Collection is immutable and cannot be changed.");
  }


  public function offsetUnset($offset) {
    throw new \BadMethodCallException("Collection is immutable and cannot be changed.");
  }

  //! @endcond

  
  
}
<?php


/**
 * @file IndexMemberTask.php
 * @brief This file contains the IndexMemberTask class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Task;


use ReIndex\Model\Member;
use ReIndex\Collection;

use EoC\Opt\ViewQueryOpts;

use Phalcon\Di;


/**
 * @brief
 * @nosubgrouping
 */
class IndexMemberTask implements ITask {
  private $member;

  protected $di; // Stores the default Dependency Injector.
  protected $couch; // Stores the Elephant on Couch Client instance.
  protected $redis; // Stores the Redis client instance.
  protected $log; // Store the logger instance.
  protected $user; // Stores the current user.


  /**
   * @brief Constructor.
   * @param[in] Member $member A member.
   */
  public function __construct(Member $member) {
    $this->di = Di::getDefault();
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];
    $this->log = $this->di['log'];

    $this->user = $this->di['guardian']->getUser();

    $this->member = $member;
  }


  /**
   * @brief Returns a full name suitable for string comparison.
   * @return string
   */
  private function getMemberFullName() {
    $fullName = $this->member->firstName . $this->member->lastName;

    // We add the ID because someone might have used a name that matches username.
    return empty($fullName) ? $this->member->username . ':' . $this->member->id : strtolower($fullName) . ':' . $this->member->id;
  }


  /**
   * @brief Returns an inverted full name suitable for string comparison.
   * @retval string
   */
  private function getMemberInvFullName() {
    $invFullName = $this->member->lastName . $this->member->firstName;

    // We add the ID because someone might have used a name that matches username.
    return empty($invFullName) ? $this->member->username . ':' . $this->member->id : strtolower($invFullName) . ':' . $this->member->id;
  }


  public function execute() {
    $key = $this->member->id . Member::MR_HASH;

    // Returns the values associated with the specified fields in the hash stored at member ID.
    // For every field that does not exist in the hash, a nil value is returned. Because a non-existing keys are treated
    // as empty hashes, running `hMGet()` against a non-existing key will return a list of `null` values.
    $hash = $this->redis->hMGet($key, ['username', 'fullName', 'invFullName']);

    $username = $this->member->username;
    $fullName = $this->getMemberFullName();
    $invFullName = $this->getMemberInvFullName();

    // Username or full name has been changed or the member has never been indexed.
    if ($hash['username'] != $username or $hash['fullName'] != $fullName) {
      $opts = new ViewQueryOpts();
      $opts->doNotReduce()->setKey([$this->user->id]);
      $rows = $this->couch->queryView("friendships", "approvedPerMember", NULL, $opts);

      $this->redis->multi();

      foreach ($rows as $row) {
        $memberId = $row['id'][1];
        $friendId = $this->member->id;

        $this->redis->zRem($memberId . Collection\FriendCollection::TS_SET, $friendId);
        $this->redis->zRem($memberId . Collection\FriendCollection::UN_SET, $hash['username'].':'.$friendId);
        $this->redis->zRem($memberId . Collection\FriendCollection::FN_SET, $hash['fullName'].':'.$friendId);
        $this->redis->zRem($memberId . Collection\FriendCollection::IFN_SET, $hash['invFullName'].':'.$friendId);

        $this->redis->zAdd($memberId . Collection\FriendCollection::TS_SET, $row['value'], $friendId);
        $this->redis->zAdd($memberId . Collection\FriendCollection::UN_SET, 0, $hash['username'].':'.$friendId);
        $this->redis->zAdd($memberId . Collection\FriendCollection::FN_SET, 0, $hash['fullName'].':'.$friendId);
        $this->redis->zAdd($memberId . Collection\FriendCollection::IFN_SET, 0, $hash['invFullName'].':'.$friendId);
      }

      $this->redis->hMSet($this->member->id . Member::MR_HASH, ['username' => $username, 'fullName' => $fullName, 'invFullName' => $invFullName]);

      $this->redis->exec();
    }
  }

}
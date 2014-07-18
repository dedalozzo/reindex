<?php

/**
 * @file TVote.php
 * @brief This file contains the TVote trait.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Extension;


use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Model\Accessory\Vote;
use PitPress\Model\User\User;

use Phalcon\DI;


/**
 * @brief Implements the IVote interface.
 */
trait TVote {


  /**
   * @brief Returns `true` if the user has voted else otherwise.
   * @param[in] User $user The current user logged in.
   * @param[out] string $voteId The vote ID.
   * @return bool
   */
  protected function didUserVote(User $user, &$voteId = NULL) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([$this->id, $user->id]);

    $result = $this->couch->queryView("votes", "perPostAndUser", NULL, $opts);

    if ($result->isEmpty())
      return FALSE;
    else {
      $voteId = $result[0]['id'];
      return TRUE;
    }
  }


  /**
   * @brief Registers, replaces or deletes the vote.
   * @param[in] User $user The current user logged in.
   * @param[in] string $value The vote.
   * @return int The voting status.
   */
  protected function vote(User $user, $value) {
    if (is_null($user))
      return IVote::NO_USER_LOGGED_IN;

    $voted = $this->didUserVote($user, $voteId);

    if ($voted) {
      // Gets the vote.
      $vote = $this->couch->getDoc(Couch::STD_DOC_PATH, $voteId);

      // Calculates difference in seconds.
      $seconds = time() - $vote->getTimestamp();

      $di = DI::getDefault();
      $votingGracePeriod = $di['config']['application']['votingGracePeriod'];

      // The user has a grace period to change or undo his vote.
      if ($seconds <= $votingGracePeriod && !$vote->hasBeenRecorded()) {

        // The user clicked twice on the same button to undo his vote (or like).
        if ($vote->value === $value) {
          $this->couch->deleteDoc(Couch::STD_DOC_PATH, $voteId, $vote->rev);
          return IVote::DELETED;
        }
        else {
          $vote->setValue($value);
          $this->couch->saveDoc($vote);
          return IVote::REPLACED;
        }

      }
      else
        return IVote::UNCHANGED;
    }
    else {
      $vote = Vote::create($this->getType(), $this->getSection(), $this->id, $user->id, $value);
      $this->couch->saveDoc($vote);
      return IVote::REGISTERED;
    }
  }


  public function voteUp(User $user) {
    return $this->vote($user, 1);
  }


  public function voteDown(User $user) {
    return $this->vote($user, -1);
  }


  public function like(User $user) {
    return $this->vote($user, 1);
  }


  public function getScore() {
    $opts = new ViewQueryOpts();
    $opts->setKey($this->id);

    return $this->couch->queryView("votes", "perPost", NULL, $opts)->getReducedValue();
  }


  public function getUsersHaveVoted() {

    // Gets the users have voted the item.
    $opts = new ViewQueryOpts();
    $opts->setKey($this->id);
    $result = $this->couch->queryView("users", "haveVoted", NULL, $opts);

    if ($result->isEmpty())
      return [];

    // Gets the users information: display name and email.
    $keys = array_column($result->asArray(), 'value');
    $opts->reset();
    $opts->doNotReduce();
    $users = $this->couch->queryView("users", "allNames", $keys, $opts);

    $entries = [];
    foreach ($users as $user) {
      $entry = new \stdClass();
      $entry->id = $user['id'];

      // We just need the e-mail to get the Gravatar link.
      $entry->username = $user['value'][0];
      $entry->gravatar = User::getGravatar($user['value'][1]);

      $entries[] = $entry;
    }

    return $entries;
  }

}
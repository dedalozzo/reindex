<?php

//! @file TVote.php
//! @brief This file contains the TVote trait.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Extension;


use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Model\Accessory\Vote;
use PitPress\Model\User\User;


//! @brief Implements the IVote interface.
trait TVote {

  private function vote(User $user, $value) {
    $voted = $this->didUserVote($user, $voteId);

    if ($voted) {
      // Gets the vote.
      $vote = $this->couch->getDoc(Couch::STD_DOC_PATH, $voteId);

      // Calculates difference in seconds.
      $seconds = floor(time() / $vote->getTimestamp());

      // The user has 5 minutes to change his vote.
      if ($seconds < 300 && !$vote->hasBeenRecorded()) {
        $vote->setValue($value);
        $this->couch-saveDoc($vote);
      }
      else
        throw new \RuntimeException("Trascorsi 5 minuti non è più possibile rettificare il proprio voto.");
    }
    else {
      $vote = Vote::create($this->postType, $this->postSection, $this->id, $user->id, $value);
      $this->couch->saveDoc($vote);
    }
  }


  //! @copydoc IVote
  public function voteUp(User $user) {
    $this->vote($user, 1);
  }



  //! @copydoc IVote
  public function voteDown(User $user) {
    $this->vote($user, -1);
  }


  //! @copydoc IVote
  public function didUserVote(User $user, &$voteId = NULL) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([$this->id, $user->id]);

    $result = $this->couch->queryView("votes", "perPostAndUser", NULL, $opts);

    if (empty($result['rows']))
      return FALSE;
    else {
      $voteId = $result['rows'][0]['id'];
      return TRUE;
    }
  }


  //! @copydoc IVote
  public function getScore() {
    $opts = new ViewQueryOpts();
    $opts->setKey($this->id);

    $result = $this->couch->queryView("votes", "perPost", NULL, $opts);

    if (empty($result['rows']))
      return 0;
    else
      return $result['rows'][0]['value'];
  }


  //! @copydoc IVote
  public function getUsersHaveVoted() {

    // Gets the users have voted the item.
    $opts = new ViewQueryOpts();
    $opts->setKey($this->id);
    $rows = $this->couch->queryView("users", "haveVoted", NULL, $opts)['rows'];

    if (empty($rows))
      return [];

    // Gets the users information: display name and email.
    $keys = array_column($rows, 'value');
    $opts->reset();
    $opts->doNotReduce();
    $users = $this->couch->queryView("users", "allNames", $keys, $opts)['rows'];

    $entries = [];
    foreach ($users as $user) {
      $entry = new \stdClass();
      $entry->id = $user['id'];

      // We just need the e-mail to get the Gravatar link.
      $entry->displayName = $user['value'][0];
      $entry->gravatar = User::getGravatar($user['value'][1]);

      $entries[] = $entry;
    }

    return $entries;
  }


  //! @copydoc IVote
  public function getThumbsDirection(User $user) {
    return $this->redis->hGet($user->id, $this->id);
  }

}
//! @endcond
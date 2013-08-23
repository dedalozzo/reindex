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

  private function vote(User $user, $choice) {
    $voted = $this->didUserVote($user, $voteId);

    if ($voted) {
      // Gets the vote.
      $vote = $this->couch->getDoc(Couch::STD_DOC_PATH, $voteId);

      // Calculates difference in seconds.
      $seconds = floor(time() / $vote->getTimestamp());

      // The user has 5 minutes to change his vote.
      if ($seconds < 300) {
        $vote->setChoice($choice);
        $this->couch-saveDoc($vote);
      }
      else
        throw new \RuntimeException("Trascorsi 5 minuti non è più possibile rettificare il proprio voto.");
    }
    else {
      $vote = Vote::create($this->postType, $this->postSection, $this->id, $user->id, $choice);
      $this->couch->saveDoc($vote);
    }
  }


  //! @copydoc IVote
  public function voteUp(User $user) {
    $this->vote($user, '+');
  }



  //! @copydoc IVote
  public function voteDown(User $user) {
    $this->vote($user, '-');
  }


  //! @copydoc IVote
  public function didUserVote(User $user, &$voteId = NULL) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([$this->id, $user->id]);

    $result = $this->couch->queryView("votes", "perPost", NULL, $opts);

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
    $opts->setKey([$this->id]);

    $result = $this->couch->queryView("votes", "perPost", NULL, $opts);

    if (empty($result['rows']))
      return 0;
    else
      return $result['rows'][0]['value'];
  }


  //! @copydoc IVote
  public function getThumbsDirection(User $user) {
    return $this->redis->hGet($user->id, $this->id);
  }

}
//! @endcond
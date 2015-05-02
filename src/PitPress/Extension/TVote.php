<?php

/**
 * @file TVote.php
 * @brief This file contains the TVote trait.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Extension;


use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use PitPress\Helper\Text;
use PitPress\Model\Accessory\Vote;
use PitPress\Model\User;
use PitPress\Exception;

use Phalcon\DI;


/**
 * @brief Implements the IVote interface.
 */
trait TVote {


  /**
   * @brief Registers, replaces or deletes the vote.
   * @param[in] User $user The current user logged in.
   * @param[in] string $value The vote.
   * @retval int The voting status.
   */
  protected function vote($value) {
    if ($this->user->isGuest()) throw new Exception\NoUserLoggedInException('Nessun utente loggato nel sistema.');
    if ($this->user->match($this->creatorId)) throw new Exception\CannotVoteYourOwnPostException('Non puoi votare il tuo stesso post.');

    $voted = $this->didUserVote($voteId);

    if ($voted) {
      // Gets the vote.
      $vote = $this->couch->getDoc(Couch::STD_DOC_PATH, $voteId);

      // Calculates difference in seconds.
      $seconds = time() - $vote->modifiedAt;

      $votingGracePeriod = $this->di['config']['application']['votingGracePeriod'];

      // The user has a grace period to change or undo his vote.
      if ($seconds <= $votingGracePeriod) {

        // The user clicked twice on the same button to undo his vote (or like).
        if ($vote->value === $value) {
          // We don't call anymore deleteDoc, because Vote now inherits from Storable.
          //$this->couch->deleteDoc(Couch::STD_DOC_PATH, $voteId, $vote->rev);
          $vote->delete();
          $vote->save();
          return IVote::DELETED;
        }
        else {
          $vote->value = $value;
          $vote->save();
          return IVote::REPLACED;
        }

      }
      else
        throw new Exception\GracePeriodExpiredException("Non puoi cambiare il tuo voto perché è trascorso il tempo massimo.");
    }
    else {
      $vote = Vote::create();
      $vote->itemId = Text::unversion($this->id);
      $vote->userId = $this->user->id;
      $vote->value = $value;
      $vote->save();
      return IVote::REGISTERED;
    }
  }


  public function voteUp() {
    return $this->vote(1);
  }


  public function voteDown() {
    return $this->vote(-1);
  }


  public function like() {
    return $this->vote(1);
  }


  public function didUserVote(&$voteId = NULL) {
    // In case there is no user logged in returns false.
    if ($this->user->isGuest()) return FALSE;

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([Text::unversion($this->id), $this->user->id]);

    $result = $this->couch->queryView("votes", "perItemAndUser", NULL, $opts);

    if ($result->isEmpty())
      return FALSE;
    else {
      $voteId = $result[0]['id'];
      return TRUE;
    }
  }


  public function getScore() {
    $opts = new ViewQueryOpts();
    $opts->setKey(Text::unversion($this->id));

    return $this->couch->queryView("votes", "perItem", NULL, $opts)->getReducedValue();
  }


  public function getUsersHaveVoted() {

    // Gets the users have voted the item.
    $opts = new ViewQueryOpts();
    $opts->setKey(Text::unversion($this->id));
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


  public function getLastVoteTimestamp() {
    $opts = new ViewQueryOpts();
    $opts->setStartKey([$this->unversionId, Couch::WildCard()])->setEndKey([$this->unversionId])->setLimit(1);
    $result = $this->couch->queryView("votes", "perItemAndDate", NULL, $opts);

    return (!$result->isEmpty()) ? $result[0]['key'][1] : 0;
  }

}
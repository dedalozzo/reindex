<?php

/**
 * @file VoteCollection.php
 * @brief This file contains the VoteCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use ReIndex\Doc\ActiveDoc;
use ReIndex\Helper\Text;
use ReIndex\Doc\Vote;
use ReIndex\Doc\Member;
use ReIndex\Security\User\IUser;
use ReIndex\Exception;

use Phalcon\Di;


/**
 * @brief This class is used to represent a collection of votes.
 * @nosubgrouping
 */
class VoteCollection implements \Countable {

  /** @name Voting Status */
  //!@{

  const REGISTERED = 1; //!< The vote has been registered. You never voted before, so there is nothing to undo or replace.
  const DELETED = 2; //!< The vote has been deleted. For example you do a like then you unlike.
  const REPLACED = 3; //!< The vote has been replaced. For example you do a vote up, then you vote down.

  //!@}


  /**
   * @var Di $di
   */
  protected $di;

  /**
   * @var Couch $couch
   */
  protected $couch;

  /**
   * @var \Redis $redis
   */
  protected $redis;

  /**
   * @var IUser $user
   */
  protected $user;

  /**
   * @var ActiveDoc $doc
   */
  protected $doc;


  public function __construct(ActiveDoc $doc) {
    $this->doc = $doc;
    $this->di = Di::getDefault();
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];
  }


  /**
   * @brief Registers, replaces or deletes the vote.
   * @param[in] int $value The vote.
   * @param[in] bool $unversion When `true` removes the version from the ID. Use `false` to cast a vote for revision
   * approval.
   * @retval int The voting status.
   */
  public function cast($value, $unversion = TRUE) {
    if ($this->user->isGuest()) throw new Exception\NoUserLoggedInException('Nessun utente loggato nel sistema.');
    if ($this->user->match($this->doc->creatorId)) throw new Exception\CannotVoteYourOwnPostException('Non puoi votare il tuo stesso post.');

    $voted = $this->exists($voteId);

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
          $this->couch->deleteDoc(Couch::STD_DOC_PATH, $voteId, $vote->rev);
          return static::DELETED;
        }
        else {
          $vote->value = $value;
          $this->couch->saveDoc($vote);
          return static::REPLACED;
        }

      }
      else
        throw new Exception\GracePeriodExpiredException("Non puoi cambiare il tuo voto perché è trascorso il tempo massimo.");
    }
    else {
      $itemId = $unversion ? Text::unversion($this->doc->id) : $this->doc->id;
      $vote = Vote::cast($itemId, $this->user->getId(), $value);
      $this->couch->saveDoc($vote);
      return static::REGISTERED;
    }
  }


  /**
   * @brief Returns `true` if the member has voted else otherwise.
   * @param[out] string $voteId (optional) The vote ID.
   * @param[in] bool $unversion When `true` removes the version from the ID. Use `false` to know if the member casted a
   * vote for revision approval.
   * @retval bool
   */
  public function exists(&$voteId = NULL, $unversion = TRUE) {
    // In case there is no user logged in returns false.
    if ($this->user->isGuest()) return FALSE;

    $itemId = $unversion ? Text::unversion($this->doc->id) : $this->doc->id;

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([$itemId, $this->user->getId()]);

    $result = $this->couch->queryView("votes", "perItemAndMember", NULL, $opts);

    if ($result->isEmpty())
      return FALSE;
    else {
      $voteId = $result[0]['id'];
      return TRUE;
    }
  }


  /**
   * @brief Returns the arithmetic sum of each each vote.
   * @param[in] bool $unversion When `true` removes the version from the ID. Use `false` to cast a vote for revision
   * approval.
   * @retval int
   */
  public function count($unversion = TRUE) {
    $itemId = $unversion ? Text::unversion($this->doc->id) : $this->doc->id;

    $opts = new ViewQueryOpts();
    $opts->setKey($itemId);

    return $this->couch->queryView("votes", "perItem", NULL, $opts)->getReducedValue();
  }


  /**
   * @brief Returns the list of members have voted.
   * @param[in] bool $unversion When `true` removes the version from the ID. Use `false` to get the members voted for
   * revision approval.
   * @retval array An associative array.
   */
  public function getVoters($unversion = TRUE) {
    $itemId = $unversion ? Text::unversion($this->doc->id) : $this->doc->id;

    // Gets the members have voted the item.
    $opts = new ViewQueryOpts();
    $opts->setKey($itemId);
    $result = $this->couch->queryView("members", "haveVoted", NULL, $opts);

    if ($result->isEmpty())
      return [];

    // Gets the members information: display name and email.
    $keys = array_column($result->asArray(), 'value');
    $opts->reset();
    $opts->doNotReduce();
    $members = $this->couch->queryView("members", "allNames", $keys, $opts);

    $entries = [];
    foreach ($members as $member) {
      $entry = new \stdClass();
      $entry->id = $member['id'];

      // We just need the e-mail to get the Gravatar link.
      $entry->username = $member['value'][0];
      $entry->gravatar = Member::getGravatar($member['value'][1]);

      $entries[] = $entry;
    }

    return $entries;
  }


  /**
   * @brief Returns the timestamp of the last vote casted.
   * @retval int
   * @attention This method supports only unversion IDs.
   */
  public function getLastVoteTimestamp() {
    $unversionId = Text::unversion($this->doc->id);

    $opts = new ViewQueryOpts();
    $opts->setStartKey([$unversionId, Couch::WildCard()])->setEndKey([$unversionId])->setLimit(1);
    $result = $this->couch->queryView("votes", "perItemAndDate", NULL, $opts);

    return (!$result->isEmpty()) ? $result[0]['key'][1] : 0;
  }

}
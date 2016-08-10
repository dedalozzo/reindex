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
 *
 * @cond HIDDEN_SYMBOLS
 *
 * @property string $onCastVote // Stores the name of the event handler function fired just after a vote has been casted.
 *
 * @endcond
 */
final class VoteCollection implements \Countable {

  /** @name Constants */
  //!@{

  const REGISTERED = 1; //!< The vote has been registered. You never voted before, so there is nothing to undo or replace.
  const DELETED = 2;    //!< The vote has been deleted. For example you do a like then you unlike.
  const REPLACED = 3;   //!< The vote has been replaced. For example you do a vote up, then you vote down.

  //!@}
  
  private $fnOnCastVote;
  
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
    $this->user = $this->di['guardian']->getUser();
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];
  }


  /**
   * @brief Given the parameters returns the item ID.
   * @param[in] bool $unversion When `true` removes the version from the ID. Use `false` to cast a vote for revision
   * approval.
   * @param[in] string $action A member can vote for a specific action, like approve a revision or delete it.
   * @retval string
   */
  private function getItemId($unversion, $action) {
    // Unversion the ID just in case `$unversion` is `true`.
    $id = $unversion ? Text::unversion($this->doc->id) : $this->doc->id;

    // Appends the `$action` when provided.
    return empty($action) ? $id : $id . '::' . $action;
  }


  /**
   * @brief Registers, replaces or deletes the vote.
   * @param[in] int $vote The vote.
   * @param[in] bool $unversion (optional) When `true` removes the version from the ID. Use `false` to cast a vote for
   * revision approval.
   * @param[in] string $action (optional) A member can vote for a specific action, like approve a revision or delete it.
   * @param[in] string $reason (optional) The reason for the vote's preference.
   * @retval int The voting status.
   */
  public function cast($vote, $unversion = TRUE, $action = '', $reason = '') {
    if ($this->user->isGuest()) throw new Exception\NoUserLoggedInException('Nessun utente loggato nel sistema.');

    if (property_exists($this->doc, 'creatorId') && $this->user->match($this->doc->creatorId))
      throw new Exception\CannotVoteYourOwnPostException('Non puoi votare il tuo stesso post.');

    $fire = $unversion && empty($action) && isset($this->fnOnCastVote);

    $voted = $this->exists($voteId, $unversion);

    if ($voted) {
      // Gets the vote.
      $voteObj = $this->couch->getDoc('votes', Couch::STD_DOC_PATH, $voteId);

      // Calculates difference in seconds.
      $seconds = time() - $voteObj->timestamp;

      $votingGracePeriod = $this->di['config']['application']['votingGracePeriod'];

      // The user has a grace period to change or undo his vote.
      if ($seconds <= $votingGracePeriod) {

        // The user clicked twice on the same button to undo his vote.
        if ($voteObj->value === $vote) {
          $this->couch->deleteDoc('votes', Couch::STD_DOC_PATH, $voteId, $voteObj->rev);

          if ($fire)
            $this->doc->{$this->fnOnCastVote}(-$vote);

          return static::DELETED;
        }
        else {
          $voteObj->value = $vote;
          $voteObj->reason = $reason;
          $voteObj->timestamp = time();
          $this->couch->saveDoc('votes', $voteObj);

          if ($fire)
            $this->doc->{$this->fnOnCastVote}($voteObj->value - $vote);

          return static::REPLACED;
        }

      }
      else
        throw new Exception\GracePeriodExpiredException("Non puoi cambiare il tuo voto perché è trascorso il tempo massimo.");
    }
    else {
      $itemId = $this->getItemId($unversion, $action);
      $voteObj = Vote::cast($itemId, $this->user->getId(), $vote, $reason);
      $this->couch->saveDoc('votes', $voteObj);

      if ($fire)
        $this->doc->{$this->fnOnCastVote}($vote);

      return static::REGISTERED;
    }
  }


  /**
   * @brief Returns `true` if the member has voted, `false` otherwise.
   * @param[out] string $voteId (optional) The vote ID.
   * @param[in] bool $unversion (optional) When `true` removes the version from the ID. Use `false` to know if the member
   * casted a vote for revision approval.
   * @param[in] string $action (optional) A member can vote for a specific action, like approve a revision or delete it.
   * @retval bool
   */
  public function exists(&$voteId = NULL, $unversion = TRUE, $action = '') {
    // In case there is no user logged in returns false.
    if ($this->user->isGuest()) return FALSE;

    $itemId = $this->getItemId($unversion, $action);

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([$itemId, $this->user->getId()]);

    // votes/perItemAndMember/view
    $result = $this->couch->queryView('votes', 'perItemAndMember', 'view', NULL, $opts);

    if ($result->isEmpty())
      return FALSE;
    else {
      $voteId = $result[0]['id'];
      return TRUE;
    }
  }


  /**
   * @brief Returns the arithmetic sum of each each vote.
   * @param[in] bool $unversion (optional) When `true` removes the version from the ID. Use `false` to cast a vote for
   * revision approval.
   * @param[in] string $action (optional) A member can vote for a specific action, like approve a revision or delete it.
   * @retval int
   */
  public function count($unversion = TRUE, $action = '') {
    $itemId = $this->getItemId($unversion, $action);

    $opts = new ViewQueryOpts();
    $opts->setKey($itemId);

    // votes/perItem/view
    return $this->couch->queryView('votes', 'perItem', 'view', NULL, $opts)->getReducedValue();
  }


  /**
   * @brief Returns the list of members have voted.
   * @param[in] bool $unversion When `true` removes the version from the ID. Use `false` to get the members voted for
   * revision approval.
   * @param[in] string $action (optional) A member can vote for a specific action, like approve a revision or delete it.
   * @retval array An associative array.
   */
  public function getVoters($unversion = TRUE, $action = '') {
    $itemId = $this->getItemId($unversion, $action);

    // Gets the members have voted the item.
    $opts = new ViewQueryOpts();
    $opts->setKey($itemId);

    // votes/members/view
    $result = $this->couch->queryView('votes', 'members', 'view', NULL, $opts);

    if ($result->isEmpty())
      return [];

    // Gets the members information: display name and email.
    $keys = array_column($result->asArray(), 'value');
    $opts->reset();
    $opts->doNotReduce();
    // members/names/view
    $members = $this->couch->queryView('members', 'names', 'view', $keys, $opts);

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
    // votes/perItemAndEditingDate/view
    $result = $this->couch->queryView('votes', 'perItemAndEditingDate', 'view', NULL, $opts);

    return (!$result->isEmpty()) ? $result[0]['key'][1] : 0;
  }

  
  //! @cond HIDDEN_SYMBOLS

  public function getOnCastVote() {
    return $this->fnOnCastVote;
  }


  public function issetOnCastVote() {
    return isset($this->fnOnCastVote);
  }


  public function setOnCastVote(callable $callable) {
    $this->fnOnCastVote = $callable;
  }


  public function unsetOnCastVote() {
    unset($this->fnOnCastVote);
  }

  //! @endcond

}
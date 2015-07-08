<?php

/**
 * @file IVote.php
 * @brief This file contains the IVote interface.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Extension;


/**
 * @brief Defines voting methods.
 * @nosubgrouping
 */
interface IVote {

  /** @name Voting Status */
  //!@{

  const REGISTERED = 1; //!< The vote has been registered. You never voted before, so there is nothing to undo or replace.
  const DELETED = 2; //!< The vote has been deleted. For example you do a like then you unlike.
  const REPLACED = 3; //!< The vote has been replaced. For example you do a vote up, then you vote down.

  //!@}


  /** @name Voting Methods */
  //!@{

  /**
   * @brief Registers, replaces or deletes the vote.
   * @param[in] int $value The vote.
   * @param[in] bool $unversion When `true` removes the version from the ID. Use `false` to cast a vote for revision
   * approval.
   * @retval int The voting status.
   */
  public function vote($value, $unversion = TRUE);


  /**
   * @brief Vote up a post.
   * @retval int The voting status.
   * @attention This method can't be used to cast a vote for revision approval.
   */
  public function voteUp();


  /**
   * @brief Vote down a post.
   * @retval int The voting status.
   * @attention This method can't be used to cast a vote for revision approval.
   */
  public function voteDown();


  /**
   * @brief Likes a post. Same as voteUp().
   * @retval int The voting status.
   * @attention This method can't be used to cast a vote for revision approval.
   */
  public function like();


  /**
   * @brief Returns `true` if the user has voted else otherwise.
   * @param[out] string $voteId (optional) The vote ID.
   * @param[in] bool $unversion When `true` removes the version from the ID. Use `false` to know if the user casted a
   * vote for revision approval.
   * @retval bool
   */
  public function didUserVote(&$voteId = NULL, $unversion = TRUE);


  /**
   * @brief Returns the arithmetic sum of each each vote.
   * @param[in] bool $unversion When `true` removes the version from the ID. Use `false` to cast a vote for revision
   * approval.
   * @retval int
   */
  public function getScore($unversion = TRUE);


  /**
   * @brief Returns the list of users have voted.
   * @param[in] bool $unversion When `true` removes the version from the ID. Use `false` to get the users voted for
   * revision approval.
   * @retval array An associative array.
   */
  public function getUsersHaveVoted($unversion = TRUE);


  /**
   * @brief Returns the timestamp of the last vote casted.
   * @retval int
   * @attention This method supports only unversion IDs.
   */
  public function getLastVoteTimestamp();

  //!@}

} 
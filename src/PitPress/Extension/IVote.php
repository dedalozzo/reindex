<?php

/**
 * @file IVote.php
 * @brief This file contains the IVote interface.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Extension;


use PitPress\Model\User;


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
   * @brief Likes a post. Same as voteUp().
   * @return int The voting status.
   */
  public function like();


  /**
   * @brief Vote up a post.
   * @return int The voting status.
   */
  public function voteUp();


  /**
   * @brief Vote down a post.
   * @return int The voting status.
   */
  public function voteDown();


  /**
   * @brief Returns `true` if the user has voted else otherwise.
   * @param[out] string $voteId The vote ID.
   * @return bool
   */
  public function didUserVote(&$voteId = NULL);


  /**
   * @brief Returns the arithmetic sum of each each vote.
   * @return int
   */
  public function getScore();


  /**
   * @brief Returns the list of users have voted.
   * @return array An associative array.
   */
  public function getUsersHaveVoted();

  //!@}

} 
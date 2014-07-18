<?php

/**
 * @file IVote.php
 * @brief This file contains the IVote interface.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Extension;


use PitPress\Model\User\User;


/**
 * @brief Defines voting methods.
 * @nosubgrouping
 */
interface IVote {

  /** @name Voting Status */
  //!@{
  const DELETED = -1; //!< The vote has been deleted. For example you do a like then you unlike.
  const REPLACED = 2; //!< The vote has been replaced. For example you do a vote up, then you vote down.
  const UNCHANGED = 0; //!< The vote hasn't changed. You tried to replace the vote too late.
  const REGISTERED = 1; //!< The vote has been registered. You never voted before, so there is nothing to undo or replace.
  //!@}


  /** @name Voting Methods */
  //!@{

  /**
   * @brief Likes a post.
   * @param[in] User $user The current user logged in.
   * @return int The voting status.
   */
  public function like(User $user);


  /**
   * @brief Vote up a post.
   * @param[in] User $user The current user logged in.
   * @return int The voting status.
   */
  public function voteUp(User $user);


  /**
   * @brief Vote down a post.
   * @param[in] User $user The current user logged in.
   * @return int The voting status.
   */
  public function voteDown(User $user);


  /**
   * @brief Returns `true` if the user has voted else otherwise.
   * @param[in] User $user The current user logged in.
   * @param[out] string $voteId The vote ID.
   * @return bool
   */
  public function didUserVote(User $user, &$voteId = NULL);


  /**
   * @brief Returns the arithmetic sum of each each vote.
   * @return integer
   */
  public function getScore();


  /**
   * @brief Returns the list of users have voted.
   * @return array An associative array.
   */
  public function getUsersHaveVoted();


  /**
   * @brief Returns the thumbs state expressed by the current user in relation to the current post.
   * @param[in] User $user The current user logged in.
   * @return string|boolean Returns `false` in case the user never voted, '+' for thumbs up and '-' for thumbs down.
   */
  public function getThumbsDirection(User $user);

  //!@}

} 
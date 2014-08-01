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
  const NO_USER_LOGGED_IN = -1; //!< No user logged in. The user is a guest.
  const CANNOT_VOTE_YOUR_OWN_POST = -2; //!< The user cannot vote a post that belongs to himself.
  const UNCHANGED = 0; //!< The vote hasn't changed. You tried to replace the vote too late.
  const REGISTERED = 1; //!< The vote has been registered. You never voted before, so there is nothing to undo or replace.
  const DELETED = 2; //!< The vote has been deleted. For example you do a like then you unlike.
  const REPLACED = 3; //!< The vote has been replaced. For example you do a vote up, then you vote down.
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
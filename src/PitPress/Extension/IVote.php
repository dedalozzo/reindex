<?php

//! @file IVote.php
//! @brief This file contains the IVote interface.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Extension;


use PitPress\Model\User\User;


//! @brief Defines voting methods.
//! @nosubgrouping
interface IVote {

  //! @name Voting Methods
  // @{

  //! @brief Likes an post.
  //! @param[in] User $user The current user logged in.
  public function voteUp(User $user);


  //! @brief Unlikes a post.
  //! @param[in] User $user The current user logged in.
  public function voteDown(User $user);


  //! @brief Returns `true` if the user has voted else otherwise.
  //! @param[in] User $user The current user logged in.
  //! @return boolean
  public function didUserVote(User $user, &$voteId = NULL);


  //! @brief Returns the arithmetic sum of each each vote.
  //! @return integer
  public function getScore();


  //! @brief Returns the thumbs state expressed by the current user in relation to the current post.
  //! @param[in] User $user The current user logged in.
  //! @return string|boolean Returns `false` in case the user never voted, '+' for thumbs up and '-' for thumbs down.
  public function getThumbsDirection(User $user);

  //@}

} 
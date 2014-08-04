<?php

/**
 * @file IStar.php
 * @brief This file contains the IStar interface.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Extension;


use PitPress\Model\User\User;


/*
 * @brief Defines starring methods.
 * @nosubgrouping
 */
interface IStar {

  /** @name Starring Status */
  //!@{
  const STARRED = 1; //!< The post has been added to your favorites.
  const UNSTARRED = 2; //!< The vote has been removed from your favorites.
  //!@}


  /** @name Starring Methods */
  //!@{

  /**
   * @brief Returns `true` if the current user starred this post.
   * @param[in] User $user The current user logged in.
   * @param[out] string $starId The star document identifier related to the current post.
   * @return bool
   */
  public function isStarred(User $user, &$starId = NULL);


  /**
   * @brief Adds or removes the item to the favourites list of the current user.
   * @param[in] User $user The current user logged in.
   */
  public function star(User $user);


  /**
   * @brief Returns the number of times the item has been starred.
   * @return integer
   */
  public function getStarsCount();

  //!@}

} 
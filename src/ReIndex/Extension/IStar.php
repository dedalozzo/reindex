<?php

/**
 * @file IStar.php
 * @brief This file contains the IStar interface.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Extension;


/**
 * @brief Defines starring methods.
 * @nosubgrouping
 */
interface IStar {

  /** @name Starring Status */
  //!@{

  const STARRED = 1; //!< The post has been added to your favorites.
  const UNSTARRED = 2; //!< The post has been removed from your favorites.

  //!@}


  /** @name Starring Methods */
  //!@{

  /**
   * @brief Returns `true` if the current user starred this post.
   * @param[out] string $starId (optional) The star document ID related to the current post.
   * @retval bool
   */
  public function isStarred(&$starId = NULL);


  /**
   * @brief Adds or removes the item to the favourites list of the current user.
   */
  public function star();


  /**
   * @brief Returns the number of times the item has been starred.
   * @retval integer
   */
  public function getStarsCount();

  //!@}

} 
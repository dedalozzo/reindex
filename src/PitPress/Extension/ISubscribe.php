<?php

/**
 * @file ISubscribe.php
 * @brief This file contains the ISubscribe interface.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Extension;


use PitPress\Model\User\User;


/**
 * @brief Defines subscribing methods.
 * @nosubgrouping
 */
interface ISubscribe {

  /*** @name Subscribing Methods */
  //!@{

  /**
   * @brief Returns `true` if the user has subscribed the current post.
   * @param[in] User $user The current user logged in.
   * @return boolean
   */
  public function isSubscribed(User $user, &$subscriptionId = NULL);


  /**
   * @brief The current user will get notifications about changes related to the current item.
   * @param[in] User $user The current user logged in.
   */
  public function subscribe(User $user);


  /**
   * @brief The current user won't get notifications anymore.
   * @param[in] User $user The current user logged in.
   */
  public function unsubscribe(User $user);


  /**
   * @brief Returns the number of users have been subscribed the item.
   * @return integer
   */
  public function getSubscribersCount();

  //!@}

}
<?php

/**
 * @file ISubscribe.php
 * @brief This file contains the ISubscribe interface.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Extension;


use ReIndex\Model\Member;


/**
 * @brief Defines subscribing methods.
 * @nosubgrouping
 */
interface ISubscribe {

  /** @name Subscribing Methods */
  //!@{

  /**
   * @brief Returns `true` if the user has subscribed the current post.
   * @param[in] Member $user The current user logged in.
   * @param[in] string $subscriptionId (optional) The subscription document ID.
   * @retval boolean
   */
  public function isSubscribed(Member $user, &$subscriptionId = NULL);


  /**
   * @brief The current user will get notifications about changes related to the current item.
   * @param[in] Member $user The current user logged in.
   */
  public function subscribe(Member $user);


  /**
   * @brief The current user won't get notifications anymore.
   * @param[in] Member $user The current user logged in.
   */
  public function unsubscribe(Member $user);


  /**
   * @brief Returns the number of members have been subscribed the item.
   * @retval integer
   */
  public function getSubscribersCount();

  //!@}

}
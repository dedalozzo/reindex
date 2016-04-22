<?php

/**
 * @file IConsumer.php
 * @brief This file contains the Consumer interface.
 * @details
 * @author Filippo F. Fadda
 */


//! oAuth2 consumers
namespace ReIndex\Security\Consumer;


/**
 * @brief This interface describes a consumer.
 * @nosubgrouping
 */
interface IConsumer {

  /**
   * @brief Returns `true` in case the linked provider is trustworthy, `false` otherwise.
   * @retval bool
   */
  function isTrustworthy();


  /**
   * @brief The authenticated user joins the ReIndex social network.
   * @retval Model::Member An user instance.
   */
  function join();


  /**
   * @brief Retrieves the user friends.
   * @retval array
   */
  function getFriends();


  /**
   * @brief Returns the consumer name.
   * @retval string
   */
  function getName();


  /**
   * @brief Returns the data scope.
   * @retval array
   */
  function getScope();

}
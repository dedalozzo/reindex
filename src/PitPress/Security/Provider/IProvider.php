<?php

//! @file IProvider.php
//! @brief This file contains the IProvider class.
//! @details
//! @author Filippo F. Fadda


//! OAuth service providers namespace.
namespace PitPress\Security\Provider;


use PitPress\Model\User;


/**
 * @brief This interface defines common methods between every class who represent an OAuth Service Provider.
 * @nosubgrouping
 */
interface IProvider {


  /**
   * @brief Returns the provider's name.
   * @return string
   */
  function getName();


  /**
   * @brief Returns the user's id.
   * @return string
   */
  function getId();


  /**
   * @brief Returns the username.
   * @return string
   */
  function getUsername();


  /**
   * @brief Returns the user's first name.
   * @return string
   */
  function getFirstName();


  /**
   * @brief Returns the user's last name.
   * @return string
   */
  function getLastName();


  /**
   * @brief Returns the user's language setting.
   * @return string
   */
  function getLocale();


  /**
   * @brief Returns the user's time offset.
   * @return integer
   */
  function getTimeOffSet();


  /**
   * @brief Returns `true` in case the user's email has been verified, `false` otherwise.
   * @return string
   */
  function isVerified();


  /**
   * @brief Returns the user's emails.
   * @return array
   */
  function getEmails();


  /**
   * @brief Returns the user friends list.
   * @return array
   */
  function getFriends();


  /**
   * @brief Returns extra data.
   * @return array
   */
  function getExtra();
}
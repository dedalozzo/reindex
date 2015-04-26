<?php

/**
 * @file IUser.php
 * @brief This file contains the IUser class.
 * @details
 * @author Filippo F. Fadda
 */


//! PitPress users namespace.
namespace PitPress\Security\User;


/**
 * @brief This interface defines common methods between every class who represent an user.
 * @nosubgrouping
 */
interface IUser {


  /**
   * @brief Returns the user id if any, otherwise `null`.
   * @retval string|bool
   */
  function getId();


  /**
   * @brief Returns `true` in case the user is a guest.
   * @retval bool
   */
  function isGuest();


  /**
   * @brief Returns `true` in case the user is a community's member.
   * @retval bool
   */
  function isMember();


  /**
   * @brief Returns `true` in case the user is an administrator.
   * @retval bool
   */
  function isAdmin();


  /**
   * @brief Returns `true` in case the user is a moderator.
   * @retval bool
   */
  function isModerator();


  /**
   * @brief Returns `true` in case the user is a reviewer.
   * @details An user can obtain this privilege earning reputation.
   * @retval bool
   */
  function isReviewer();


  /**
   * @brief Returns `true` in case the user is an editor.
   * @details An user can obtain this privilege earning reputation.
   * @retval bool
   */
  function isEditor();

}
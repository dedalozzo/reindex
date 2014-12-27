<?php

//! @file IUser.php
//! @brief This file contains the IUser class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Security;


/**
 * @brief This interface defines common methods between every class who represent an user.
 * @nosubgrouping
 */
interface IUser {


  /**
   * @brief Returns the user id if any, otherwise `null`.
   * @return string|bool
   */
  function getId();


  /**
   * @brief Returns `true` if the user has been confirmed.
   * @return bool
   */
  function isConfirmed();


  /**
   * @brief Returns `true` in case the user is a guest.
   * @return bool
   */
  function isGuest();


  /**
   * @brief Returns `true` in case the user is a community's member.
   * @return bool
   */
  function isMember();


  /**
   * @brief Returns `true` in case the user is a moderator.
   * @return bool
   */
  function isModerator();


  /**
   * @brief Returns `true` in case the user is an administrator.
   * @return bool
   */
  function isAdmin();


  /**
   * @brief Returns `true` in case the user is an editor.
   * @details An user can obtain this privilege earning reputation.
   * @return bool
   */
  function isEditor();


  /**
   * @brief Returns `true` in case the user is a reviewer.
   * @details An user can obtain this privilege earning reputation.
   * @return bool
   */
  function isReviewer();

}
<?php

/**
 * @file IUser.php
 * @brief This file contains the IUser class.
 * @details
 * @author Filippo F. Fadda
 */


//! Classes and interfaces to represent users and members.
namespace ReIndex\Security\User;


use ReIndex\Security\Role\Permission\IPermission;


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
   * @brief Returns `true` if the provided user id matches the current one, `false` otherwise.
   * @details This method is useful to check the ownership of a post, for example.
   * @param[in] string $id The id to match.
   * @retval bool
   */
  function match($id);


  /**
   * @brief Returns `true` if the provided user has the permission to execute a specific operation, `false` otherwise.
   * @param[in] IPermission $permission A permission.
   * @retval bool
   */
  function has(IPermission $permission);


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

}
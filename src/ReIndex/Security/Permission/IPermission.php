<?php

/**
 * @file IPermission.php
 * @brief This file contains the IPermission interface.
 * @details
 * @author Filippo F. Fadda
 */


//! Permissions
namespace ReIndex\Security\Role\Permission;


use ReIndex\Security\Role\IRole;


/**
 * @brief You may implement this interface to create a new permission class.
 * @details Permissions are hereditary, that means you may have the same permission for the role member and admin. The
 * admin version of that permission will override the method `checkForAdminRole()`. See below.
 * To apply different strategies in relation to the role associated to the member you must implement a method for that
 * particular role. Such a method must use as prefix `checkFor`, followed by the role's name. For example, to apply two
 * different strategies for both a member and a moderator you must implement two public methods: `checkForMemberRole()`
 * and `checkForModeratorRole()`.
 * @nosubgrouping
 */
interface IPermission {


  /**
   * @brief Magic method __toString.
   * @return string
   */
  function __toString();


  /**
   * @brief Returns the permission's name.
   * @return string
   */
  function getName();


  /**
   * @brief Returns the permission's description.
   * @return string
   */
  function getDescription();


  /**
   * @brief Sets the permission's execution role.
   * @param[in] Role::IRole $role The relative role.
   */
  function setRole(IRole $role);


  /**
   * @brief Returns the permission's execution role.
   * @return Role::IRole
   */
  function getRole();

}
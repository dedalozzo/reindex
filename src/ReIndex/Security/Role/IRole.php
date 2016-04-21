<?php

/**
 * @file IRole.php
 * @brief This file contains the IRole interface.
 * @details
 * @author Filippo F. Fadda
 */


//! Roles that a member may assume
namespace ReIndex\Security\Role;


/**
 * @brief You may implement this interface to create a new role class.
 * @details Roles are hereditary, that means AdminRole is a subclass of ModeratorRole. When you grant a role to a member,
 * the less important role is replaced with the most important one. In the previous example, since the AdminRole is a
 * subclass of ModeratorRole, the AdminRole will replace the ModeratorRole instance. There are cases when you have
 * different roles, since each role can perform a set of specific operations. For example a DeveloperRole instance has
 * the permission to debug and to show the phpinfo. Remember that you can grant to a member any role you want.
 * You can even extend any role or you can create a new role class in yuor own plugin. Just be sure that the permission
 * related to the new role are inside a directory with the role name. In case you are creating a new permission for a
 * role previously defined, you have to follow the same strategy. The php file that contains the class must be in a
 * subdirectory with the name of the role is related. Its namespace must include the role name as well. For example
 * if you add a new permission for the DeveloperRole, you must use the following structure (for both the namespace and
 * the directory):
 * `YourNamespace\WhateverYouWant\DeveloperRole\ProfilePermission`.
 * @nosubgrouping
 */
interface IRole  {


  /**
   * @brief Magic method __toString.
   * @return string
   */
  function __toString();


  /**
   * @brief Returns the role's name.
   * @return string
   */
  function getName();


  /**
   * @brief Returns the role's description.
   * @return string
   */
  function getDescription();

}

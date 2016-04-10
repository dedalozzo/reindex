<?php
/**
 * @file IRole.php
 * @brief This file contains the IRole interface.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role;


use ReIndex\Security\Role\Operation\IOperation;


/**
 * @brief You may implement this interface to create a new role class.
 * @details Roles are hereditary, that means Admin is a subclass of Moderator. When you grant a role to a member, the
 * less important role is replaced with the most important. In the previous example, since Admin is a subclass of
 * Moderator, the Admin role will replace the Moderator role. There are cases when you have different roles, since each
 * role can perform a set of operations, you can grant to a member any role you want.
 * @nosubgrouping
 */
interface IRole  {


  /**
   * @brief Returns the role name.
   * @return string
   */
  function getName();


  /**
   * @brief Returns the role description.
   * @return string
   */
  function getDescription();


  /**
   * @brief Grants the permission to the current role.
   * @details If another permission with the same name exists, replaces it.
   * @param[in] string $name The permission name.
   * @param[in] string $class The class name of the permission, included its namespace.
   */
  function grantPermission($name, $class);


  /**
   * @brief Revokes the permission associated to the current role.
   * @param[in] string The operation name.
   */
  function revokePermission($name);


  /**
   * @brief Returns `true` if the permission is granted for this role, `false` otherwise.
   * @param[in] string The permission name.
   */
  function permissionExists($name);


  /**
   * @brief Given the permission name returns the associated permission class..
   * @param[in] string The permission name.
   */
  function obtainPermissionClass($name);


  /**
   * @brief Magic method __toString.
   * @return string
   */
  function __toString();

}

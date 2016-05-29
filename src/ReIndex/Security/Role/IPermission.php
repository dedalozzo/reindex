<?php
/**
 * @file IPermission.php
 * @brief This file contains the IPermission interface.
 * @details
 * @author Filippo F. Fadda
 */


//! Permissions
namespace ReIndex\Security\Role;


/**
 * @brief You may implement this interface to create a new permission class.
 * @details Permissions are hereditary, that means you may have the same permission for the role member and admin. The
 * admin version of that permission will override the method `check()`.
 * A permission is associated to a role when it shares the same namespace's root. For example a permission for an Admin
 * must have the namespace `Reindex\Security\Role\Admin\PermissionName`
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
   * @retval Role::IRole
   */
  function getRole();


  /**
   * @brief Returns the permission's execution context.
   * @retval mixed
   */
  function getContext();


  /**
   * @brief Sets the permission's execution context.
   * @param[in] mixed $context The execution context.
   */
  function setContext($context);


  /**
   * @brief Checks if the current user has the permission to perform the operation requested.
   * @retval mixed
   */
  function check();


  /**
   * @brief Casts the object to the specified subclass.
   * @details This function is used internally to cast the permission object to a subclass instance. It may happen that
   * the current member is associated with a superior role, which uses a subclass of the permission class, that overrides
   * the `check()` method. Casting the permission object to a subclass instance allows to apply different strategies in
   * relation to the role associated to the member.
   * @param[in] string $newClass The subclass name, included its namespace.
   * @return IPermission
   */
  function castAs($newClass);

}
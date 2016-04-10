<?php
/**
 * @file IRole.php
 * @brief This file contains the IRole interface.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role;


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
   * @brief Magic method __toString.
   * @return string
   */
  function __toString();

}

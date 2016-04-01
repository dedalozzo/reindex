<?php
/**
 * @file IRole.php
 * @brief This file contains the IRole interface.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role;


/**
 * @brief You may implement this interface to create a new role.
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

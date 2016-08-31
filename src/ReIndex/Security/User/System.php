<?php

/**
 * @file System.php
 * @brief This file contains the System class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\User;


use ReIndex\Security\Role\Permission\IPermission;


/**
 * @brief A special user used to perform special task.
 * @nosubgrouping
 */
final class System implements IUser {


  /**
   * @brief This implementation returns always `null`.
   * @retval null
   */
  public function getId() {
    return NULL;
  }


  /**
   * @brief This implementation returns always `false`.
   * @param[in] string $id The id to match.
   * @retval bool
   */
  public function match($id) {
    return FALSE;
  }


  /**
   * @brief This implementation returns always `true`.
   * @retval bool
   */
  public function has(IPermission $permission) {
    return TRUE;
  }


  /**
   * @brief This implementation returns always `false`.
   * @retval bool
   */
  public function isGuest() {
    return FALSE;
  }


  /**
   * @brief This implementation returns always `false`.
   * @retval bool
   */
  public function isMember() {
    return FALSE;
  }

}
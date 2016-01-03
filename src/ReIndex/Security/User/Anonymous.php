<?php

/**
 * @file Anonymous.php
 * @brief This file contains the Anonymous class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\User;


/**
 * @brief This class represents an anonymous user.
 * @nosubgrouping
 */
class Anonymous implements IUser {


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
  public function isGuest() {
    return TRUE;
  }


  /**
   * @brief This implementation returns always `false`.
   * @retval bool
   */
  public function isMember() {
    return FALSE;
  }


  /**
   * @brief This implementation returns always `false`.
   * @retval bool
   */
  public function isAdmin() {
    return FALSE;
  }


  /**
   * @brief This implementation returns always `false`.
   * @retval bool
   */
  public function isDeveloper() {
    return FALSE;
  }


  /**
   * @brief This implementation returns always `false`.
   * @retval bool
   */
  public function isModerator() {
    return FALSE;
  }


  /**
   * @brief This implementation returns always `false`.
   * @retval bool
   */
  public function isEditor() {
    return FALSE;
  }


  /**
   * @brief This implementation returns always `false`.
   * @retval bool
   */
  public function isReviewer() {
    return FALSE;
  }

}
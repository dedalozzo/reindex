<?php

/**
 * @file System.php
 * @brief This file contains the System class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Security\User;


/**
 * @brief A special user used to perform special task.
 * @nosubgrouping
 */
class System implements IUser {


  /**
   * @brief This implementation returns always `null`.
   * @retval null
   */
  public function getId() {
    return NULL;
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


  /**
   * @brief This implementation returns always `true`.
   * @retval bool
   */
  public function isAdmin() {
    return TRUE;
  }


  /**
   * @brief This implementation returns always `true`.
   * @retval bool
   */
  public function isModerator() {
    return TRUE;
  }


  /**
   * @brief This implementation returns always `true`.
   * @retval bool
   */
  public function isEditor() {
    return TRUE;
  }


  /**
   * @brief This implementation returns always `true`.
   * @retval bool
   */
  public function isReviewer() {
    return TRUE;
  }

}
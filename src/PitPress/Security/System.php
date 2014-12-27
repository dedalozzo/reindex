<?php

//! @file System.php
//! @brief This file contains the System class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Security;


/**
 * @brief A special user used to perform special task.
 * @nosubgrouping
 */
class System implements IUser {


  /**
   * @brief This implementation returns always `null`.
   * @return null
   */
  public function getId() {
    return NULL;
  }


  /**
   * @brief This implementation returns always `true`.
   * @return bool
   */
  public function isConfirmed() {
    return TRUE;
  }


  /**
   * @brief This implementation returns always `false`.
   * @return bool
   */
  public function isGuest() {
    return FALSE;
  }


  /**
   * @brief This implementation returns always `false`.
   * @return bool
   */
  public function isMember() {
    return FALSE;
  }


  /**
   * @brief This implementation returns always `true`.
   * @return bool
   */
  public function isModerator() {
    return TRUE;
  }


  /**
   * @brief This implementation returns always `true`.
   * @return bool
   */
  public function isAdmin() {
    return TRUE;
  }


  /**
   * @brief This implementation returns always `true`.
   * @return bool
   */
  public function isEditor() {
    return TRUE;
  }


  /**
   * @brief This implementation returns always `true`.
   * @return bool
   */
  public function isReviewer() {
    return TRUE;
  }

}
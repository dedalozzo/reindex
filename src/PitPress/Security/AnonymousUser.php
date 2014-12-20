<?php

//! @file AnonymousUser.php
//! @brief This file contains the AnonymousUser class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Security;


/**
 * @brief This class represents an anonymous user.
 * @nosubgrouping
 */
class AnonymousUser implements IUser {


  /**
   * @brief This implementation returns always `null`.
   * @return null
   */
  public function getId() {
    return NULL;
  }


  /**
   * @brief This implementation returns always `false`.
   * @return bool
   */
  public function isConfirmed() {
    return FALSE;
  }


  /**
   * @brief This implementation returns always `true`.
   * @return bool
   */
  public function isGuest() {
    return TRUE;
  }


  /**
   * @brief This implementation returns always `false`.
   * @return bool
   */
  public function isModerator() {
    return FALSE;
  }


  /**
   * @brief This implementation returns always `false`.
   * @return bool
   */
  public function isAdmin() {
    return FALSE;
  }


  /**
   * @brief This implementation returns always `false`.
   * @return bool
   */
  public function isEditor() {
    return FALSE;
  }


  /**
   * @brief This implementation returns always `false`.
   * @return bool
   */
  public function isReviewer() {
    return FALSE;
  }

}
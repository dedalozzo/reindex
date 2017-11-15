<?php

/**
 * @file EmailCollection.php
 * @brief This file contains the EmailCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use Meta\MetaCollection;


/**
 * @brief This class is used to represent a collection of e-mails.
 * @nosubgrouping
 */
final class EmailCollection extends MetaCollection {


  /**
   * @brief Adds an e-mail address to the current member.
   * @param[in] string $email An e-mail address.
   * @param[in] bool $verified The e-mail address has been verified.
   */
  public function add($email, $verified = FALSE) {
    $this->meta[$this->name][$email] = $verified;

    if (count($this->meta[$this->name]) == 1)
      $this->meta['primaryEmail'] = $email;
  }


  /**
   * @brief Removes, if possible, the specified e-mail address from the list of e-mail addresses associated to the
   * current member.
   * @param[in] string $email An e-mail address.
   */
  public function remove($email) {
    if ($this->canRemove($email))
      unset($this->meta[$this->name][$email]);
  }


  /**
   * @brief Returns `true` in case the e-mail can be removed, `false` otherwise.
   * @details An e-mail can be removed only if it's not the only one associated to the member, and if there is at least
   * another verified e-mail and if it's not the primary e-mail.
   * @param[in] string $email An e-mail address.
   * @retval bool
   */
  public function canRemove($email) {
    if (array_key_exists($email, $this->meta[$this->name]) && count($this->meta[$this->name]) > 1
      && $email != $this->meta['primaryEmail']
      && (!$this->meta[$this->name][$email] or count(array_filter($this->meta[$this->name])) > 1))
      return TRUE;
    else
      return FALSE;
  }


  /**
   * @brief Returns `true` if the e-mail address is already present, `false` otherwise.
   * @param[in] string $email An e-mail address.
   * @retval bool
   */
  public function exists($email) {
    return isset($this->meta[$this->name][$email]);
  }


  /**
   * @brief Returns `true` if the member e-mail is primary, `false` otherwise.
   * @param[in] string $email An e-mail address.
   * @retval bool
   */
  public function isPrimary($email) {
    return ($this->meta['primaryEmail'] === $email);
  }


  /**
   * @brief Sets the e-mail as the primary address.
   * @param[in] string $email An e-mail address.
   */
  public function setPrimary($email) {
    $this->meta['primaryEmail'] = $email;
  }


  /**
   * @brief Returns the primary e-mail address.
   * @retval string
   */
  public function getPrimary() {
    return $this->meta['primaryEmail'];
  }


  /**
   * @brief Returns `true` if the member e-mail has been verified, `false` otherwise.
   * @param[in] string $email An e-mail address.
   * @retval bool
   */
  public function isVerified($email) {
    return (isset($this->meta['emails'][$email])) ? $this->meta['emails'][$email] : FALSE;
  }

}
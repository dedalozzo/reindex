<?php

/**
 * @file Friendship.php
 * @brief This file contains the Friendship class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use EoC\Doc\Doc;

use ReIndex\Exception;



/**
 * @brief This class is used to store the friendship between two different members.
 * @nosubgrouping
 */
class Friendship extends Doc {


  /**
   * @brief Creates an instance of Friendship class.
   * @param[in] string $senderId The sender ID of the member requested the friendship.
   * @param[in] string $receiverId The receiver ID of the member requested the friendship.
   */
  public static function request($senderId, $receiverId) {
    $instance = new self();

    $instance->meta["senderId"] = $senderId;
    $instance->meta["receiverId"] = $receiverId;
    $instance->meta["approved"] = FALSE;

    return $instance;
  }


  /**
   * @brief Approves the friendship.
   * @attention Instead of reject a friendship, you have to simply delete it.
   */
  public function approve() {
    $this->meta["approve"] = TRUE;
  }


  /**
   * @brief Returns `true` if a friendship has been approved, false otherwise.
   * @attention Due to the internal `friendships/perMember` implementation, this method is pretty useless.
   * @retval bool
   */
  public function isApproved() {
    return $this->meta["approved"];
  }


  //! @cond HIDDEN_SYMBOLS

  public function getSenderId() {
    return $this->meta['senderId'];
  }


  public function issetSenderId() {
    return isset($this->meta['senderId']);
  }


  public function setSenderId($value) {
    $this->meta['senderId'] = $value;
  }


  public function unsetSenderId() {
    if ($this->isMetadataPresent('senderId'))
      unset($this->meta['senderId']);
  }
  

  public function getReceiverId() {
    return $this->meta['receiverId'];
  }


  public function issetReceiverId() {
    return isset($this->meta['receiverId']);
  }


  public function setReceiverId($value) {
    $this->meta['receiverId'] = $value;
  }


  public function unsetReceiverId() {
    if ($this->isMetadataPresent('receiverId'))
      unset($this->meta['receiverId']);
  }

  public function getRequestedAt() {
    return $this->meta['requestedAt'];
  }


  public function issetRequestedAt() {
    return isset($this->meta['requestedAt']);
  }


  public function setRequestedAt($value) {
    $this->meta['requestedAt'] = $value;
  }


  public function unsetRequestedAt() {
    if ($this->isMetadataPresent('requestedAt'))
      unset($this->meta['requestedAt']);
  }
  

  public function getApprovedAt() {
    return $this->meta['approvedAt'];
  }


  public function issetApprovedAt() {
    return isset($this->meta['approvedAt']);
  }


  public function setApprovedAt($value) {
    $this->meta['approvedAt'] = $value;
  }


  public function unsetApprovedAt() {
    if ($this->isMetadataPresent('approvedAt'))
      unset($this->meta['approvedAt']);
  }

  //! @endcond

}
<?php

/**
 * @file Friendship.php
 * @brief This file contains the Friendship class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use EoC\Doc\Doc;


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
    $instance->meta["requestedAt"] = time();

    return $instance;
  }


  /**
   * @brief Approves the friendship.
   * @attention Instead of reject a friendship, you have to simply delete it.
   */
  public function approve() {
    $this->meta["approve"] = TRUE;
    $this->meta["approvedAt"] = time();
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


  public function getReceiverId() {
    return $this->meta['receiverId'];
  }


  public function issetReceiverId() {
    return isset($this->meta['receiverId']);
  }


  public function getRequestedAt() {
    return $this->meta['requestedAt'];
  }


  public function issetRequestedAt() {
    return isset($this->meta['requestedAt']);
  }


  public function getApprovedAt() {
    return $this->meta['approvedAt'];
  }


  public function issetApprovedAt() {
    return isset($this->meta['approvedAt']);
  }

  //! @endcond

}
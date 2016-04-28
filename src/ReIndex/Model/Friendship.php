<?php

/**
 * @file Friendship.php
 * @brief This file contains the Friendship class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Model;


use EoC\Doc\Doc;

use ReIndex\Exception;


/**
 * @brief This class is used to store the friendship between two different members.
 * @nosubgrouping
 */
class Friendship extends Doc {


  /**
   * @brief Creates an instance of Friendship class.
   * @param[in] Member $sender The sender of the friendship request.
   * @param[in] Member $receiver The receiver of the friendship request.
   */
  public static function request(Member $sender, Member $receiver) {
    $instance = new self();

    $instance->meta["senderId"] = $sender->id;
    $instance->meta["receiverId"] = $receiver->id;
    $instance->meta["approved"] = FALSE;
    $instance->meta["requestedAt"] = time();
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


  public function getReceiverId() {
    return $this->meta['receiverId'];
  }


  public function getRequestedAt() {
    return $this->meta['requestedAt'];
  }


  public function getApprovedAt() {
    return $this->meta['approvedAt'];
  }

  //! @endcond

}
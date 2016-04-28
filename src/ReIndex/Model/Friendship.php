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
   */
  public static function request(Member $member, $approved = FALSE) {
    $instance = new self();

    $instance->meta["senderId"] = $instance->receiver->id;
    $instance->meta["receiverId"] = $member->id;
    $instance->meta["approved"] = $approved;

    $instance->meta["requestedAt"] = time();

    if ($approved)
      $instance->meta["approvedAt"] = $instance->meta["requestedAt"];
  }


  public function approve() {
    $this->meta["approvedAt"] = time();
  }


  public function isApproved() {
    return $this->meta["approved"];
  }


  /**
   * @brief Saves the friendship to the database.
   */
  public function save() {
    $this->couch->saveDoc($this);
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

  
  public function getApproved() {
    return $this->meta['approved'];
  }


  public function issetApproved() {
    return isset($this->meta['approved']);
  }


  public function setApproved($value) {
    $this->meta['approved'] = $value;
  }


  public function unsetApproved() {
    if ($this->isMetadataPresent('approved'))
      unset($this->meta['approved']);
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
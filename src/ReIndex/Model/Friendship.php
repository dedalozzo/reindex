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

    $instance->meta["senderId"] = $instance->user->id;
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


  public function reject() {
    if ($this->user->isGuest()) throw new Exception\NoUserLoggedInException('Nessun utente loggato nel sistema.');

    if (!$this->user->match($this->receiverId))
      throw new Exception\UserMismatchException("You can't reject someone else's friendship.");

    if ($this->meta["approved"])
      throw new \RuntimeException("You can't reject a friendship request you have already approved.");

    // This is not really necessary.
    $this->meta['approved'] = FALSE;

    $this->delete();
    $this->save();
  }


  public function withdraw() {
    if ($this->user->isGuest()) throw new Exception\NoUserLoggedInException('Nessun utente loggato nel sistema.');

    if (!$this->user->match($this->receiverId))
      throw new Exception\UserMismatchException("You can't reject someone else's friendship.");
    
    
    $this->reject();
  }
  
  
  public function cancel() {
    
  }


  /**
   * @brief Saves the friendship to the database.
   */
  public function save() {
    $this->couch->saveDoc($this);
  }

}
<?php

//! @file Vote.php
//! @brief This file contains the Vote class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\Accessory;


use ElephantOnCouch\Doc\Doc;


//! @brief This class is used to keep trace of the user votes.
//! @nosubgrouping
class Vote extends Doc {

  //! @brief Creates an instance of Vote class.
  public static function create($postType, $postSection, $postId, $userId, $value) {
    $instance = new self();

    $instance->meta["postType"] = $postType;
    $instance->meta["postSection"] = $postSection;
    $instance->meta["postId"] = $postId;
    $instance->meta["userId"] = $userId;
    $instance->meta["recorded"] = FALSE;
    $instance->setValue($value);

    return $instance;
  }


  public function getPostId() {
    return $this->meta["postId"];
  }


  public function getPostSection() {
    return $this->meta["section"];
  }


  public function getUserId() {
    return $this->meta["userid"];
  }


  public function getValue() {
    return $this->meta["value"];
  }


  public function setValue($value) {
    $this->meta["value"] = $value;
    $this->meta["timestamp"] = time();
  }


  public function getTimestamp() {
    return $this->meta["timestamp"];
  }


  //! @brief Returns `true`if the vote has been recorded by the PitPress daemon.
  //! @return boolean
  public function hasBeenRecorded() {
    return $this->meta["recorded"];
  }

}
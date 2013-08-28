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
  public static function create($postType, $postSection, $postId, $userId, $choice) {
    $instance = new self();

    $instance->meta["postType"] = $postType;
    $instance->meta["postSection"] = $postSection;
    $instance->meta["postId"] = $postId;
    $instance->meta["userId"] = $userId;
    $instance->meta["recorded"] = FALSE;
    $instance->setChoice($choice);

    return $instance;
  }


  //! @brief Returns the user choice.
  //! @return string
  public function getChoice() {
    return $this->meta['choice'];
  }


  //! @brief Changes the user vote and update timestamp.
  //! @param[in] string $value The user vote.
  public function setChoice($value) {
    $this->meta['choice'] = $value;
    $this->meta["timestamp"] = time();
  }


  //! @brief Returns the voting timestamp.
  //! @return integer
  public function getTimestamp() {
    return $this->meta["timestamp"];
  }

}
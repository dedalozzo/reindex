<?php

//! @file Score.php
//! @brief This file contains the Score class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\Accessory;


use ElephantOnCouch\Doc\Doc;


//! @brief This class is used to record the score of a every single post.
//! @nosubgrouping
class Score extends Doc {


  //! @brief Creates an instance of Vote class.
  public static function create($postType, $postSection, $postId, $postTimestamp, $points) {
    $instance = new self();

    $instance->meta["postType"] = $postType;
    $instance->meta["postSection"] = $postSection;
    $instance->meta["postId"] = $postId;

    $instance->meta["postYear"] = date('Y', $postTimestamp);
    $instance->meta["postMonth"] = date('m', $postTimestamp);
    $instance->meta["postDay"] = date('d', $postTimestamp);

    $instance->meta["points"] = $points;

    return $instance;
  }


  //! @brief Sums the provided points to the score.
  //! @param[in] $value The points to add.
  public function addPoints($value) {
    $this->meta["points"] += $value;
  }

}
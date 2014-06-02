<?php

/**
 * @file Vote.php
 * @brief This file contains the Vote class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Accessory;


use ElephantOnCouch\Doc\Doc;


/**
 * @brief This class is used to keep trace of the user votes.
 * @nosubgrouping
 */
class Vote extends Doc {

  /**
   * @brief Creates an instance of Vote class. This parameter is not required, you can provide NULL.
   * @param[in] $postType The post type.
   * @param[in] $postSection The post section.
   * @param[in] $postId The post identifier. Can be also an item.
   * @param[in] $userId The identifier of the user who has voted.
   * @param[in] $value The value of the vote.
   * @return An instance of Vote class.
   */
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


  /**
   * @brief Returns `true`if the vote has been recorded by the PitPress daemon.
   * @return boolean
   */
  public function hasBeenRecorded() {
    return $this->meta["recorded"];
  }


  /**
   * @brief Marks the vote has recorded.
   */
  public function markAsRecorded() {
    $this->meta["recorded"] = TRUE;
  }


  //! @cond HIDDEN_SYMBOLS

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

  //! @endcond

}
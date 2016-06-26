<?php

/**
 * @file Follower.php
 * @brief This file contains the Follower class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use EoC\Doc\Doc;


/**
 * @brief This class is used to represent a follower.
 * @nosubgrouping
 */
class Follower extends Doc {


  /**
   * @brief Creates an instance of Follower class.
   * @param[in] string $memberId The sender ID of the member requested the friendship.
   * @param[in] string $followerId The receiver ID of the member requested the friendship.
   */
  public static function create($memberId, $followerId) {
    $instance = new self();

    $instance->meta["memberId"] = $memberId;
    $instance->meta["followerId"] = $followerId;

    return $instance;
  }


  //! @cond HIDDEN_SYMBOLS

  public function getMemberId() {
    return $this->meta['memberId'];
  }


  public function issetMemberId() {
    return isset($this->meta['memberId']);
  }


  public function setMemberId($value) {
    $this->meta['memberId'] = $value;
  }


  public function unsetMemberId() {
    if ($this->isMetadataPresent('memberId'))
      unset($this->meta['memberId']);
  }
  

  public function getFollowerId() {
    return $this->meta['followerId'];
  }


  public function issetFollowerId() {
    return isset($this->meta['followerId']);
  }


  public function setFollowerId($value) {
    $this->meta['followerId'] = $value;
  }


  public function unsetFollowerId() {
    if ($this->isMetadataPresent('followerId'))
      unset($this->meta['followerId']);
  }
  
  //! @endcond

}
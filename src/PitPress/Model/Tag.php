<?php

//! @file Tag.php
//! @brief This file contains the Tag class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model;


//! @brief
//! @nosubgrouping
class Tag extends Item {

  public function getFollowersCount() {

  }


  public function subscribe() {

  }


  public function unsubscribe() {

  }


  public function setExcerpt($value) {
    $this->meta["excerpt"] = $value;
  }


  public function getExcerpt() {
    return $this->meta["excerpt"];
  }


}
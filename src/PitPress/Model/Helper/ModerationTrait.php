<?php

//! @file ModerationTrait.php
//! @brief This file contains the ModerationTrait trait.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\Helper;


use PitPress\Model\Helper\ItemState;


//! @cond HIDDEN_SYMBOLS
trait ModerationTrait {

  //! @brief Gets the item state.
  public function getState() {
    return $this->meta['state'];
  }

  
  //! @brief Submits the item for publishing.
  public function submit() {
    $this->meta['state'] = ItemState::SUBMITTED;
    $this->save();
  }


  //! @brief Asks the author to revise the item, because it's not ready for publishing.
  //! @details The post will be automatically deleted in 10 days.
  public function reject($reason) {
    // todo: send a notification to the user
    $this->meta['state'] = ItemState::REJECTED;
    $this->save();
  }


  //! @brief Publishes the item on line, making visible to everyone.
  public function publish() {
    $this->meta['state'] = ItemState::PUBLISHED;
    $this->meta["publishingDate"] = time();

    $this->save();
  }

}
//! @endcond
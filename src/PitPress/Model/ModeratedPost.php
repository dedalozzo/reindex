<?php

//! @file ModeratedPost.php
//! @brief This file contains the ModeratedPost class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model;


//! @brief This class represent a post that needs moderators attention.
//! @nosubgrouping
abstract class ModeratedPost extends Post {

  //! @name Different states a moderated post may assume.
  //@{
  const SUBMITTED = "submitted"; //!< The item has been submitted for publishing.
  const PUBLISHED = "published"; //!< The item has been published.
  const REJECTED = "rejected"; //!< The item has been rejected.
  //@}


  //! @name Moderating Methods
  //@{

  //! @brief Gets the item state.
  public function getState() {
    return $this->meta['state'];
  }


  //! @brief Submits the item for publishing.
  public function submit() {
    $this->meta['state'] = self::SUBMITTED;
    $this->save();
  }


  //! @brief Asks the author to revise the item, because it's not ready for publishing.
  //! @details The post will be automatically deleted in 10 days.
  public function reject($reason) {
    // todo: send a notification to the user
    $this->meta['state'] = self::REJECTED;
    $this->save();
  }


  //! @brief Publishes the item on line, making visible to everyone.
  public function publish() {
    $this->meta['state'] = self::PUBLISHED;
    $this->meta["publishingDate"] = time();

    $this->save();
  }

  //@}

}
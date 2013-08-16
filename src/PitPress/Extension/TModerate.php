<?php

//! @file TModerate.php
//! @brief This file contains the TModerate trait.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Extension;


use PitPress\Enum\PostState;


//! @brief Implements the IModerate interface.
trait TModerate {

  //! @name Moderating Methods
  //@{

  //! @copydoc IModerate
  public function getState() {
    return $this->meta['state'];
  }


  //! @copydoc IModerate
  public function submit() {
    $this->meta['state'] = PostState::SUBMITTED;
    $this->save();
  }


  //! @copydoc IModerate
  public function reject($reason) {
    // todo: send a notification to the user
    $this->meta['state'] = PostState::REJECTED;
    $this->save();
  }


  //! @copydoc IModerate
  public function publish() {
    $this->meta['state'] = PostState::PUBLISHED;
    $this->meta["publishingDate"] = time();

    $this->save();
  }


  //! @copydoc IModerate
  public function markAsDraft() {
    $this->meta['state'] = PostState::DRAFT_STATE;
    $this->save();
  }

  //@}

}
<?php

//! @file TPin.php
//! @brief This file contains the TPin trait.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Extension;


//! @brief Implements IPin interface.
trait TPin {


  //! @copydoc IPin
  public function pin() {
    if ($this->isPublished) {
      $this->meta['pinned'] = TRUE;
      $this->save();
    }
  }


  //! @copydoc IPin
  public function unpin() {
    $this->meta['pinned'] = FALSE;
    $this->save();
  }


  //! @copydoc IPin
  public function isPinned() {
    return $this->meta["pinned"];
  }

} 
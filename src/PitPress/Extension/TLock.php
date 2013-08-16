<?php

//! @file TLock.php
//! @brief This file contains the TLock trait.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Extension;


//! @brief Implements ILock interface.
trait TLock {

  //! @copydoc ILock
  public function lock() {

  }


  //! @copydoc ILock
  public function unlock() {

  }


  //! @copydoc ILock
  public function isLocked() {
    return $this->meta["locked"];
  }

} 
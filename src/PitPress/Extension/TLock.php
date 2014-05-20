<?php

//! @file TLock.php
//! @brief This file contains the TLock trait.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Extension;


//! @brief Implements ILock interface.
//! @copydoc ILock
trait TLock {


  public function lock() {

  }


  public function unlock() {

  }


  public function isLocked() {
    return $this->meta["locked"];
  }

} 
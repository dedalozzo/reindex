<?php

//! @file ILock.php
//! @brief This file contains the ILock interface.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Extension;


//! @brief Defines locking methods.
interface ILock {

  //! @name Moderating Methods
  //@{

  //! @brief
  public function lock();


  //! @brief
  public function unlock();


  //! @brief Returns `true` if any user can't post comments or answers.
  public function isLocked();

  //@}

} 
<?php

//! @file ItemState.php
//! @brief This file contains the ItemState class.
//! @details
//! @author Filippo F. Fadda


//! @brief This namespace contains helper classes and traits.
namespace PitPress\Model\Helper;


//! @brief An helper class tha contains useful constants.
class ItemState {

  //! @name Different states an item can assume.
  //@{
  const SUBMITTED = "submitted"; //!< The item has been submitted for publishing.
  const PUBLISHED = "published"; //!< The item has been published.
  const REJECTED = "rejected"; //!< The item has been rejected.
  //@}

}
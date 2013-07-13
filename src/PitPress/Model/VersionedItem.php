<?php

//! @file Mode
//! @brief This file contains the Item class.
//! @details
//! @author Filippo F. Fadda


//! @brief PitPress models namespace.
namespace PitPress\Model;


//! @brief This class represents an abstract versioned item.
//! @nosubgrouping
abstract class VersionedItem extends Item {


  //! @brief Retrieves the list of changes.
  public function getChanges() {

  }


  //! @brief Rollbacks to the specified version.
  public function rollback($version) {

  }

}
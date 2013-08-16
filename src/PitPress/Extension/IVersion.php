<?php

//! @file IVersion.php
//! @brief This file contains the IVersion interface.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Extension;


//! @brief Defines control versioning methods.
//! @nosubgrouping
interface IVersion {

  //! @name Control Versioning Methods
  //@{

  //! @brief Retrieves the list of changes.
  public function getChanges();


  //! @brief Rollbacks to the specified version.
  public function rollback($version);

  //! @}

} 
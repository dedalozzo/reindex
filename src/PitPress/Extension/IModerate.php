<?php

//! @file IModerate.php
//! @brief This file contains the IModerate interface.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Extension;


//! @brief Defines moderating methods.
//! @nosubgrouping
interface IModerate {

  //! @name Moderating Methods
  //@{

  //! @brief Gets the item state.
  public function getState();


  //! @brief Submits the item for publishing.
  public function submit();


  //! @brief Asks the author to revise the item, because it's not ready for publishing.
  //! @details The post will be automatically deleted in 10 days.
  public function reject($reason);


  //! @brief Publishes the item on line, making visible to everyone.
  public function publish();


  //! @brief Marks the item as draft.
  //! @details When a user works on an article, he wants save many time the item before submit it for publishing.
  public function markAsDraft();

  //@}

} 
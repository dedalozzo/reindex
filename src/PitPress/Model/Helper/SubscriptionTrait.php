<?php

//! @file SubscriptionTrait.php
//! @brief This file contains the SubscriptionTrait trait.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\Helper;


use PitPress\Model\User\User;


//! @cond HIDDEN_SYMBOLS
trait SubscriptionTrait {


  //! @brief The current user will get notifications about changes related to the current item.
  public function subscribe(User $currentUser) {

  }


  //! @brief The current user won't get notifications anymore.
  public function unsubscribe(User $currentUser) {

  }

}
//! @endcond
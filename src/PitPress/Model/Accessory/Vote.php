<?php

//! @file Vote.php
//! @brief This file contains the Vote class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\Accessory;


use ElephantOnCouch\Doc\Doc;


//! @brief This class is used to keep trace of the user favourites.
//! @details Every user can add a document to his favourites. So we need to know the favourites list, and we can do it
//! using a view that emit as key the user id and as value the item id. We also need a way to show if a particular item
//! has been added to the logged user. Another view will emit as key the user id and as value the item id. This is a
//! classic many-to-many relationship.
//! @nosubgrouping
class Vote extends Doc {

  public function __construct($itemId, $userId, $sign = "+") {
    $this->meta["itemId"] = $itemId;
    $this->meta["userId"] = $userId;
    $this->meta["sign"] = $sign;
  }

}
<?php

//! @file Favourite.php
//! @brief This file contains the Favourite class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\Accessory;


use ElephantOnCouch\Doc\Doc;


//! @brief This class is used to keep trace of the user favourites.
//! @details Every user can add a document to his favourites. So we need to know the favourites list, and we can do it
//! using a view that emit as key the user id and as value the item id. We also need a way to show if a particular item
//! has been added by the logged user. Another view will emit as key an array composed by the item id plus the user id.
//! This is a classic many-to-many relationship.
//! Each time a document is deleted, we must delete all the favourites related; when instead a user is deleted, we must
//! delete all the emitted favourites.
//! @nosubgrouping
class Favourite extends Doc {

  public function __construct($itemId, $userId) {
    $this->meta["itemId"] = $itemId;
    $this->meta["userId"] = $userId;
    $this->meta["timestamp"] = time();
  }

}
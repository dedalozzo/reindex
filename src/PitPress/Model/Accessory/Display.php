<?php

//! @file Display.php
//! @brief This file contains the Display class.
//! @details
//! @author Filippo F. Fadda


//! @brief In this namespace you can find classes that are not intended to be used as documents, but they serve, instead,
//! to store and provide additional information.
namespace PitPress\Model\Accessory;


use ElephantOnCouch\Doc\Doc;


//! @brief This class is used to represent a single impression. Every time someone read an article, for example, you want
//! increment the total number of views, creating and saving a Display instance, passing the ID of the article a user
//! just read.
//! @nosubgrouping
class Display extends Doc {

  public function __construct($itemId) {
    $this->meta["itemId"] = $itemId;
  }

}
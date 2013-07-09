<?php

//! @file Hit.php
//! @brief This file contains the Hit class.
//! @details
//! @author Filippo F. Fadda


//! @brief In this namespace you can find classes that are not intended to be used as documents, but they serve, instead,
//! to store and provide additional information.
namespace PitPress\Model\Accessory;


use ElephantOnCouch\Doc\Doc;


//! @brief This class is used to record a single request.
//! @details Every time someone read an article, for example, you want increment the total number of views, creating and
//! saving an Hit instance.
//! @nosubgrouping
class Hit extends Doc {

  public function __construct($docId) {
    $this->meta['docId'] = $docId;
  }

}
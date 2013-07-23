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
//! @details To avoid conflicts on update, we need to emit a document every time an item has been viewed. We
//! use a script that runs every 5/10 minutes, querying a view where the hits are registered, to count the hits. All the
//! other queries will use 'stale=ok' to not refresh the view.
//! @nosubgrouping
class Hit extends Doc {

  public function __construct($docId) {
    $this->meta['docId'] = $docId;
  }

}
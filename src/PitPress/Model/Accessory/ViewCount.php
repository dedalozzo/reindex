<?php

//! @file ViewCount.php
//! @brief This file contains the ViewCount class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\Accessory;


use ElephantOnCouch\Doc\Doc;


//! @brief This class is used to register the number of times an item has been viewed.
//! @details To avoid conflicts on update, we need to store the number of views of any item in a separate document. We
//! use a script that runs every 5/10 minutes, querying a view where the hits are registered, to count the hits in a
//! date range. This script is going to run on just one server. After we run the script, we delete the documents
//! identified by the type 'hit'.
//! @nosubgrouping
class ViewCount extends Doc {

  public function __construct($docId, $docType) {
    $this->meta['docId'] = $docId;
    $this->meta['docType'] = $docType;
  }

}
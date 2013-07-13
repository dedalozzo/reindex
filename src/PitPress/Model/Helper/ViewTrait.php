<?php

//! @file ViewTrait.php
//! @brief This file contains the ViewTrait trait.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\Helper;


use PitPress\Model\Accessory\Hit;

use ElephantOnCouch\Opt\ViewQueryOpts;


//! @cond HIDDEN_SYMBOLS
trait ViewTrait {


  //! @brief Returns the times the item has been viewed.
  public function getViewsCount() {
    $opts = new ViewQueryOpts();
    $opts->setKey($this->id);

    $this->couch->queryView("hits", "all", NULL, $opts);
  }


  //! @brief Increments the times the item has been viewed.
  public function incViews() {
    // We can increment the views of a document that has been already saved.
    if (isset($this->rev)) {
      $hit = new Hit($this->id, $this->type);

      $this->couch->saveDoc($hit);
    }
  }

}
//! @endcond
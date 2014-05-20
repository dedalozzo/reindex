<?php

//! @file TCount.php
//! @brief This file contains the TCount trait.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Extension;


//! @brief Implements ICount interface.
//! @copydoc ICount
trait TCount {


  public function getHitsCount() {
    if (isset($this->rev))
      return number_format($this->redis->hGet($this->id, 'hits'), 0, ",", ".");
    else
      return 0;
  }


  public function incHits() {
    // We can increment the views of a document that has been already saved.
    if (isset($this->rev))
      $this->redis->hIncrBy($this->id, 'hits', 1);
  }

} 
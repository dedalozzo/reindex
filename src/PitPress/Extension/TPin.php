<?php

/**
 * @file TPin.php
 * @brief This file contains the TPin trait.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Extension;


/**
 * @brief Implements IPin interface.
 */
trait TPin {


  public function pin() {
    if ($this->isPublished()) {
      $this->meta['pinned'] = TRUE;
      $this->save();
    }
  }


  public function unpin() {
    $this->meta['pinned'] = FALSE;
    $this->save();
  }


  public function isPinned() {
    return $this->meta["pinned"];
  }

} 
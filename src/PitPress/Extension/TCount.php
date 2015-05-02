<?php

/**
 * @file TCount.php
 * @brief This file contains the TCount trait.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Extension;


use PitPress\Helper\Text;


/**
 * @brief Implements ICount interface.
 */
trait TCount {


  public function getHitsCount() {
    if (isset($this->rev))
      return Text::formatNumber($this->redis->hGet(Text::unversion($this->id), 'hits'));
    else
      return 0;
  }


  public function incHits($userId = NULL) {
    if (isset($userId) && $this->user->match($userId))
      return $this->getHitsCount();

    // We can increment the views of a document that has been already saved.
    if (isset($this->rev)) {
      $hits = $this->redis->hIncrBy(Text::unversion($this->id), 'hits', 1);
      return $hits;
    }
    else
      return 0;
  }

} 
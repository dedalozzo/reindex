<?php

/**
 * @file Tag.php
 * @brief This file contains the Tag class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model;


use PitPress\Extension;


/**
 * @brief A label used to categorize posts.
 * @details Every post must be tagged with a maximun of five tags.
 * @nosubgrouping
 */
class Tag extends Versionable implements Extension\ICount, Extension\IStar {
  use Extension\TCount, Extension\TStar;


  /**
   * @brief Gets the item state.
   */
  public function getState() {
    return $this->meta['state'];
  }


  //! @cond HIDDEN_SYMBOLS

  public function getName() {
    return $this->meta['name'];
  }


  public function issetName() {
    return isset($this->meta['name']);
  }


  public function setName($value) {
    $this->meta['name'] = $value;
  }


  public function unsetName() {
    if ($this->isMetadataPresent('name'))
      unset($this->meta['name']);
  }

  //! @endcond

}
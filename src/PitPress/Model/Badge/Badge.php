<?php

/**
 * @file Badge.php
 * @brief This file contains the Badge class.
 * @details
 * @author Filippo F. Fadda
 */


//! PitPress badges namespace.
namespace PitPress\Model\Badge;


use PitPress\Model\Storable;


/**
 * @brief This is the ancestor of all badges, it's abstract and can't be instantiated.
 * @details Badge implements the observer pattern.
 * @nosubgrouping
 */
class Badge extends Storable {


  //! @cond HIDDEN_SYMBOLS


  public function getUserId() {
    return $this->meta['userId'];
  }


  public function issetUserId() {
    return isset($this->meta['userId']);
  }


  public function setUserId($value) {
    $this->meta['userId'] = $value;
  }


  public function unsetTagId() {
    if ($this->isMetadataPresent('tagId'))
      unset($this->meta['tagId']);
  }


  public function getTagId() {
    return $this->meta['tagId'];
  }


  public function issetTagId() {
    return isset($this->meta['tagId']);
  }


  public function setTagId($value) {
    $this->meta['tagId'] = $value;
  }


  public function unsetUserId() {
    if ($this->isMetadataPresent('userId'))
      unset($this->meta['userId']);
  }


  public function getDecoratorClass() {
    return $this->meta['decoratorClass'];
  }


  public function issetDecoratorClass() {
    return isset($this->meta['decoratorClass']);
  }


  public function setDecoratorClass($value) {
    $this->meta['decoratorClass'] = $value;
  }


  public function unsetDecoratorClass() {
    if ($this->isMetadataPresent('decoratorClass'))
      unset($this->meta['decoratorClass']);
  }

  //! @endcond

}
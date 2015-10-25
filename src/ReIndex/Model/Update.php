<?php

/**
 * @file Link.php
 * @brief This file contains the Link class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Model;


/*
 * @brief
 * @nosubgrouping
 */
class Update extends Post {

  protected function needForApproval() {
    return TRUE;
  }


  //! @cond HIDDEN_SYMBOLS

  public function getUrl() {
    return $this->meta['url'];
  }


  public function issetUrl() {
    return isset($this->meta['url']);
  }


  public function setUrl($value) {
    $this->meta['url'] = $value;
  }


  public function unsetUrl() {
    if ($this->isMetadataPresent('url'))
      unset($this->meta['url']);
  }

  //! @endcond

}
<?php

/**
 * @file Link.php
 * @brief This file contains the Link class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model;


/*
 * @brief
 * @nosubgrouping
 */
class Link extends Post {

  protected function needForApproval() {
    return TRUE;
  }


  //! @cond HIDDEN_SYMBOLS

  public function getLanguage() {
    return $this->meta['language'];
  }


  public function issetLanguage() {
    return isset($this->meta['language']);
  }


  public function setLanguage($value) {
    $this->meta['language'] = $value;
  }


  public function unsetLanguage() {
    if ($this->isMetadataPresent('language'))
      unset($this->meta['language']);
  }


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

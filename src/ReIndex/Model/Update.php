<?php

/**
 * @file Update.php
 * @brief This file contains the Update class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Model;


/*
 * @brief This class represents an user's update.
 * @nosubgrouping
 */
class Update extends Post {


  protected function needForApproval() {
    return FALSE;
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
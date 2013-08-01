<?php

//! @file Link.php
//! @brief This file contains the Link class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\Link;


use PitPress\Model\Post;
use PitPress\Model\Helper;


//! @brief
//! @nosubgrouping
class Link extends Post {


  protected function needForApproval() {
    return TRUE;
  }


  public function getSection() {
    return 'links';
  }


  public function getHumanReadableType() {
    return 'LINK';
  }

}

<?php

//! @file Question.php
//! @brief This file contains the Item class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\Forum;


use PitPress\Model\Post;


//! @brief
//! @nosubgrouping
class Question extends Post {


  public function getSection() {
    return 'forum';
  }


  public function getPublishingType() {
    return 'DOMANDA';
  }

}

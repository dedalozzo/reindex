<?php

//! @file Question.php
//! @brief This file contains the Item class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\Forum;


use PitPress\Model\Post;


//! @brief A question asked by a user.
//! @nosubgrouping
class Question extends Post {


  public function getSection() {
    return 'forum';
  }


  public function getPublishingType() {
    return 'DOMANDA';
  }


  //! @brief Gets the related answers.
  public function getAnswers() {

  }

}

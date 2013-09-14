<?php

//! @file Tutorial.php
//! @brief This file contains the ${CLASS_NAME} class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\Blog;


use PitPress\Model\Aggregate;


//! @brief This class represents a tutorial.
//! @nosubgrouping
class Tutorial extends Aggregate {


  public function getPublishingType() {
    return 'GUIDA';
  }


  public function getSection() {
    return 'blog';
  }

} 
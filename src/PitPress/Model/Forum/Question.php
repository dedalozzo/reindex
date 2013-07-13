<?php

//! @file Question.php
//! @brief This file contains the Item class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\Forum;


use PitPress\Model\Post;
use PitPress\Model\Helper;


//! @brief
//! @nosubgrouping
class Question extends Post {
  use Helper\SubscriptionTrait;
}

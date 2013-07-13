<?php

//! @file ModeratedPost.php
//! @brief This file contains the ModeratedPost class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model;


use PitPress\Model\Helper\ModerationTrait;


//! @brief This class represent a post that needs moderators attention.
//! @nosubgrouping
abstract class ModeratedPost extends Post {
  use ModerationTrait;
}
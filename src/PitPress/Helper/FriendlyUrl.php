<?php

//! @file FriendlyUrl.php
//! @brief This file contains the FriendlyUrl class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Helper;


// This class is used to generate friendly urls.
class FriendlyUrl {

  public static function getPrettyDate($unixTimestamp) {
    return date("Y/m/d", $unixTimestamp);
  }


  public static function getPrettyTitle($title) {
    return str_replace(" ", "-", preg_replace('/[^a-z0-9]/i',' ', $title));
  }


  public static function getPrettyUrl($section, $prettyDate, $prettyTitle) {
    return "/".$section."/".$prettyDate."/".$prettyTitle.".html";
  }

} 
<?php

//! @file Url.php
//! @brief This file contains the Url class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Helper;


/**
 * @brief This helper class contains routines to process urls.
 * @nosubgrouping
 */
class Url {

  /**
   * @brief Builds the post url, given its publishing or creation date and its slug.
   * @param[in] int $date Publishing or creation date.
   * @param[in] string $slug The slug of the title.
   * @return string The complete url of the post.
   */
  public static function build($date, $slug) {
    return date('/Y/m/d/', $date).$slug;
  }

} 
<?php

/**
 * @file Bronze.php
 * @brief This file contains the Bronze class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge;


/**
 * @brief This is the ancestor for all bronze badges.
 */
abstract class Bronze extends Badge {


  /**
   * @copydoc Badge::getMetal()
   */
  public function getMetal() {
    return "bronze";
  }

} 
<?php

/**
 * @file Gold.php
 * @brief This file contains the Gold class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge;


/**
 * @brief This is the ancestor for all gold badges.
 */
abstract class Gold extends Badge {


  //! @copydoc Badge::getMetal()
  public function getMetal() {
    return "gold";
  }

} 
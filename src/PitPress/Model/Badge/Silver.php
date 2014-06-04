<?php

/**
 * @file Silver.php
 * @brief This file contains the Silver class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge;


/*
 * @brief This is the ancestor for all silver badges.
 */
abstract class Silver extends Badge {


  //! @copydoc Badge::getMetal()
  public function getMetal() {
    return "silver";
  }

} 
<?php

/**
 * @file Electorate.php
 * @brief This file contains the Electorate class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Moderation;


use PitPress\Model\Badge\Gold;


/**
 * @brief Voted at least 500 times and 25% or more of total votes are on questions.
 * @details Awarded once.
 */
class Electorate extends Gold {


  public function award() {

  }


  public function withdrawn() {

  }

} 
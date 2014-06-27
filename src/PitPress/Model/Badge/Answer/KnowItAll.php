<?php

/**
 * @file KnowItAll.php
 * @brief This file contains the KnowItAll class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Silver;


/**
 * @brief Provided answers of 15 total score in 20 of top 40 tags.
 * @details Awarded once.
 */
class KnowItAll extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Tuttologo";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return "Fornito risposte con un punteggio di 15 in almeno la metà.";
  }


  /**
   * @copydoc Badge::award()
   */
  public function award() {

  }


  /**
   * @copydoc Badge::withdrawn()
   */
  public function withdrawn() {

  }

} 
<?php

/**
 * @file Librarian.php
 * @brief This file contains the Librarian class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Tag;


use PitPress\Model\Badge\Gold;


/**
 * @brief Edited 20 tag descriptions.
 * @details Awarded once.
 */
class Librarian extends Gold {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Bibliotecario";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return "Primo a rispondere ad una domanda.";
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
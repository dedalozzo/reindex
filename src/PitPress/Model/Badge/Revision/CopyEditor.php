<?php

/**
 * @file CopyEditor.php
 * @brief This file contains the CopyEditor class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Revision;


use PitPress\Model\Badge\Silver;


/**
 * @brief Edited 25 posts.
 * @details Awarded once.
 */
class CopyEditor extends Silver {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Copy-editor";
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
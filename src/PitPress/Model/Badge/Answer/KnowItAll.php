<?php

/**
 * @file KnowItAll.php
 * @brief This file contains the KnowItAll class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Answer;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Provided answers of 15 total score in 20 of top 40 tags.
 * @details Awarded once.
 */
class KnowItAll extends Badge {


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
    return <<<'DESC'
Hai fornito risposte con almeno 15 punti, in 20 dei 40 tag più attivi. Assegnato una sola volta.
DESC;
  }


  /**
   * @copydoc Badge::getMetal()
   */
  public function getMetal() {
    return Metal::SILVER;
  }


  /**
   * @copydoc Badge::getMessages()
   */
  public function getMessages() {
    return ['vote'];
  }


  /**
   * @copydoc Badge::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }


  /**
   * @copydoc Badge::exist()
   */
  public function exist() {

  }


  /**
   * @copydoc Badge::deserve()
   */
  public function deserve() {

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
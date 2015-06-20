<?php

/**
 * @file Superior.php
 * @brief This file contains the Superior class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Score;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Wrote a post with 25 score.
 * @details Awarded multiple times.
 */
class Superior extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Superiore";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai fornito un contributo (articolo, domanda, recensione, link) che ha ottenuto 30 punti. Assegnato più volte.
DESC;
  }


  /**
   * @copydoc Badge::getMetal()
   */
  public function getMetal() {
    return Metal::BRONZE;
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
<?php

/**
 * @file Famous.php
 * @brief This file contains the Famous class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Hit;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Wrote a post with 30.000 views.
 * @details Awarded multiple times.
 */
class Famous extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Famoso";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai scritto un articolo o formulato una domanda con almeno 30.000 visualizzazioni. Assegnato più volte.
DESC;
  }


  /**
   * @copydoc Badge::getMetal()
   */
  public function getMetal() {
    return Metal::GOLD;
  }


  /**
   * @copydoc Badge::getMessages()
   */
  public function getMessages() {
    return ['hit'];
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
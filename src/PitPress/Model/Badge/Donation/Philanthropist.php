<?php

/**
 * @file Philanthropist.php
 * @brief This file contains the Philanthropist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Donation;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Made a donation of at least 100 €.
 * @details Awarded multiple times.
 */
class Philanthropist extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Filantropo";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai fatto una donazione di almeno 100 €. Assegnato più volte.
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
    return ['donate'];
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
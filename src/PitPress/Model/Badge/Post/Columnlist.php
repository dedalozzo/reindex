<?php

/**
 * @file Columnlist.php
 * @brief This file contains the Columnlist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Post;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Wrote at least 10 articles on the same tag.
 * @details Awarded once.
 */
class Columnlist extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Articolista";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai scritto almeno 10 articoli associati ad un particolare tag. Assegnato più volte.
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
    return ['article'];
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
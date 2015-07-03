<?php

/**
 * @file Trendy.php
 * @brief This file contains the Trendy class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Badge\Star;


use PitPress\Model\Badge\Badge;
use PitPress\Enum\Metal;


/**
 * @brief Wrote a post starred by 25 users.
 * @details Awarded multiple times.
 */
class Trendy extends Badge {


  /**
   * @copydoc Badge::getName()
   */
  public function getName() {
    return "Trendy";
  }


  /**
   * @copydoc Badge::getBrief()
   */
  public function getBrief() {
    return <<<'DESC'
Hai fornito un contributo (articolo, domanda, recensione, link) aggiunto ai preferiti 25 volte. Assegnato più volte.
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
    return ['star'];
  }


  /**
   * @copydoc Badge::update()
   * @todo Implements the `update()` method.
   */
  public function update() {

  }

}
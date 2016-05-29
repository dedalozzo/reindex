<?php

/**
 * @file StarGalaxy.php
 * @brief This file contains the StarGalaxy class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Feature\Starrable;
use ReIndex\Helper\Text;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use Phalcon\Di;


/**
 * @brief This class is used to count the number of stars of a specific item.
 * @details This class implements `Countable`.
 * @nosubgrouping
 */
class StarGalaxy implements \Countable {

  /**
   * @var Couch $couch
   */
  protected $couch;

  /**
   * @var Starrable $item
   */
  protected $item;


  /**
   * @brief Creates a new galaxy of stars.
   */
  public function __construct(Starrable $item) {
    $this->item = $item;
    $this->couch = Di::getDefault()['couchdb'];
  }


  /**
   * @brief Returns the number of stars for the specified item.
   */
  public function count() {
    $opts = new ViewQueryOpts();
    $opts->setKey([Text::unversion($this->item->getId())]);

    return $this->couch->queryView("stars", "perItem", NULL, $opts)->getReducedValue();
  }
  
}
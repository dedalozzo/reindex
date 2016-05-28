<?php

/**
 * @file StarGalaxy.php
 * @brief This file contains the StarGalaxy class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


class StarGalaxy implements \Countable {

  protected $item;  // Stores the item.
  protected $couch; // Stores the CouchDB instance.


  /**
   * @brief Creates a new galaxy of stars.
   */
  public function __construct(Starrable $item) {
    $this->item = $item;
    $this->couch = Di::getDefault()['couchdb'];
  }


  public function count() {
    $opts = new ViewQueryOpts();
    $opts->setKey([Text::unversion($this->item->id)]);

    return $this->couch->queryView("stars", "perItem", NULL, $opts)->getReducedValue();
  }
  
}
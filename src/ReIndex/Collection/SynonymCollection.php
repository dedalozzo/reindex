<?php

/**
 * @file SynonymCollection.php
 * @brief This file contains the SynonymCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Doc\Synonym;

use ToolBag\Helper;
use ToolBag\Meta\MetaCollection;


/**
 * @brief This class is used to represent a collection of synonyms. It's used to manage the synonyms of any tag.
 * @nosubgrouping
 */
final class SynonymCollection extends MetaCollection {


  /**
   * @brief Adds many synonyms at once to the collection.
   * @param[in] array $synonyms An array of IDs.
   */
  protected function addMultipleAtOnce(array $synonyms) {
    $this->meta[$this->name] = Helper\ArrayHelper::merge($this->meta[$this->name], $synonyms);
  }


  /**
   * @brief Adds a synonym to the collection.
   * @param[in] Synonym $synonym A synonym object.
   */
  public function add(Synonym $synonym) {
    array_push($this->meta[$this->name], $synonym->id);
    $this->addMultipleAtOnce($synonym->getRelatedIds());
  }


  /**
   * @brief Removes the synonym from the collection.
   * @param[in] Synonym $synonym A synonym object.
   */
  public function remove(Synonym $synonym) {
    $key = array_search($synonym->name, $this->meta[$this->name]);
    if ($key !== FALSE)
      unset($this->meta[$this->name][$key]);
  }


  /**
   * @brief Returns `true` if the synonym is already present, `false` otherwise.
   * @param[in] Synonym $synonym A synonym object.
   * @retval bool
   */
  public function exists(Synonym $synonym) {
    return in_array($synonym->name, $this->meta[$this->name]);
  }

}
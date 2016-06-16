<?php

/**
 * @file SynonymCollection.php
 * @brief This file contains the SynonymCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Model\Synonym;
use ReIndex\Helper;


/**
 * @brief This class is used to represent a collection of synonyms. It's used to manage the synonyms of any tag.
 * @nosubgrouping
 */
class SynonymCollection extends MetaCollection {

  const NAME = "synonyms";


  /**
   * @brief Adds many synonyms at once to the collection.
   * @param[in] array $synonyms An array of IDs.
   */
  protected function addMultipleAtOnce(array $synonyms) {
    $this->meta[static::NAME] = Helper\ArrayHelper::merge($this->meta[static::NAME], $synonyms);
  }


  /**
   * @brief Adds a synonym to the collection.
   * @param[in] Synonym $synonym A synonym object.
   */
  public function add(Synonym $synonym) {
    array_push($this->meta[static::NAME], $synonym->id);
    $this->addMultipleAtOnce($synonym->getRelatedIds());
  }


  /**
   * @brief Removes the synonym from the collection.
   * @param[in] Synonym $synonym A synonym object.
   */
  public function remove(Synonym $synonym) {
    $key = array_search($synonym->name, $this->meta[static::NAME]);
    if ($key !== FALSE)
      unset($this->meta[static::NAME][$key]);
  }


  /**
   * @brief Returns `true` if the synonym is already present, `false` otherwise.
   * @param[in] Synonym $synonym A synonym object.
   * @retval bool
   */
  public function exists(Synonym $synonym) {
    return in_array($synonym->name, $this->meta[static::NAME]);
  }

}
<?php

/**
 * @file OrderedCollection.php
 * @brief This file contains the OrderedCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


/**
 * @brief This class is used to represent an ordered collection of keys.
 * @nosubgrouping
 */
class OrderedCollection extends MetaCollection {


  /**
   * @brief Adds the provided key to the current collection.
   * @param[in] string $key A key.
   */
  public function add($key) {
    $this->meta[$this->name][$key] = NULL;
  }


  /**
   * @brief Removes the specified key address from the collection.
   * @param[in] string $key A key.
   */
  public function remove($key) {
    if ($this->exists($key))
      unset($this->meta[$this->name][$key]);
  }


  /**
   * @brief Returns `true` if the key is already present, `false` otherwise.
   * @param[in] string $key A key.
   * @retval bool
   */
  public function exists($key) {
    return array_key_exists($key, $this->meta[$this->name]);
  }

}
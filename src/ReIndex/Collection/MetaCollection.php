<?php

/**
 * @file MetaCollection.php
 * @brief This file contains the MetaCollection class.
 * @details
 * @author Filippo F. Fadda
 */


//! Collections
namespace ReIndex\Collection;


use Phalcon\Di;


/**
 * @brief This class is used to represent a generic collection.
 * @details This class implements `IteratorAggregate`, `Countable`, and `ArrayAccess`.
 * @nosubgrouping
 */
abstract class MetaCollection implements \IteratorAggregate, \Countable, \ArrayAccess {

  const NAME = "collection";
  
  /**
   * @var array $meta
   */
  protected $meta;

  /**
   * @var Di $di
   */
  protected $di;


  /**
   * @brief Creates a new collection of e-mails.
   * @param[in] array $meta Member's array of metadata.
   */
  public function __construct(array &$meta) {
    $this->meta = &$meta;
    $this->di = Di::getDefault();
  }


  /**
   * @brief Returns the collection as a real array.
   * @retval array An associative array using as keys the e-mail addresses, and as values if the address are verified or
   * not.
   */
  public function asArray() {
    return $this->meta[static::NAME];
  }


  /**
   * @brief Returns an external iterator.
   * @retval [ArrayIterator](http://php.net/manual/en/class.arrayiterator.php).
   */
  public function getIterator() {
    return new \ArrayIterator($this->meta[static::NAME]);
  }


  /**
   * @brief Returns the number of documents found.
   * @retval integer Number of documents.
   */
  public function count() {
    return count($this->meta[static::NAME]);
  }


  /**
   * @brief Returns `true` in case there aren't items inside the collection, `false` otherwise.
   * @details Since the PHP core developers are noobs, `empty()` cannot be used on any class that implements ArrayAccess.
   * @attention This method must be used in place of `empty()`.
   * @retval bool
   */
  public function isEmpty() {
    return empty($this->meta[static::NAME]) ? TRUE : FALSE;
  }


  /**
   * @brief Whether or not an offset exists.
   * @details This method is executed when using `isset()` or `empty()` on objects implementing ArrayAccess.
   * @param[in] integer $offset An offset to check for.
   * @retval bool Returns `true` on success or `false` on failure.
   */
  public function offsetExists($offset) {
    return isset($this->meta[static::NAME][$offset]);
  }


  /**
   * @brief Returns the value at specified offset.
   * @details This method is executed when checking if offset is `empty()`.
   * @param[in] integer $offset The offset to retrieve.
   * @retval mixed Can return all value types.
   */
  public function offsetGet($offset)  {
    return $this->meta[static::NAME][$offset];
  }


  //! @cond HIDDEN_SYMBOLS

  public function offsetSet($offset, $value) {
    throw new \BadMethodCallException("Collection is immutable and cannot be changed.");
  }


  public function offsetUnset($offset) {
    throw new \BadMethodCallException("Collection is immutable and cannot be changed.");
  }

  //! @endcond

}
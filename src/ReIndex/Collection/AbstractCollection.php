<?php
/**
 * @file AbstractCollection.php
 * @brief This file contains the AbstractCollection class.
 * @details
 * @author Filippo F. Fadda
 */


//! Collections
namespace ReIndex\Collection;


/**
 * @brief This class is used to represent a generic collection.
 * @details This class implements `IteratorAggregate`, `Countable`, and `ArrayAccess`.
 * @nosubgrouping
 */
abstract class AbstractCollection implements \IteratorAggregate, \Countable, \ArrayAccess {

  protected $meta;

  const NAME = "collection";


  /**
   * @brief Creates a new collection of e-mails.
   * @param[in] array $meta Member's array of metadata.
   */
  public function __construct(array &$meta) {
    $this->meta = &$meta;
  }


  /**
   * @brief Removes from the collection the item identified by the provided key.
   * @param[in] mixed $key A key.
   */
  public function remove($key) {
    unset($this->meta[static::NAME][$key]);
  }


  /**
   * @brief Returns `true` if the key is already present, `false` otherwise.
   * @param[in] mixed $key A key.
   * @retval bool
   */
  public function exists($key) {
    return isset($this->meta[static::NAME][$key]);
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
    throw new \BadMethodCallException("Result is immutable and cannot be changed.");
  }


  public function offsetUnset($offset) {
    throw new \BadMethodCallException("Result is immutable and cannot be changed.");
  }

  //! @endcond

}
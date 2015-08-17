<?php

/**
 * @file DecoratorRecursiveFilterIterator.php
 * @brief This file contains the DecoratorRecursiveFilterIterator class.
 * @details
 * @author Filippo F. Fadda
 */


//! ReIndex filters namespace.
namespace ReIndex\Filter;


use ReIndex\Helper;


/**
 * @brief A custom filter to retrieves only the decorators.
 * @details Badges are all the classes defined under the badges' namespace (and sub-namespaces), derived
 * from Gold, Silver or TBronze classes.
 * @nosubgrouping
 */
class DecoratorRecursiveFilterIterator extends \RecursiveFilterIterator {

  #ifndef DOXYGEN_SHOULD_SKIP_THIS

  const ANCESTOR = 'ReIndex\\Model\\Badge\\Decorator\\Decorator';

  #endif


  /**
   * @brief Checks whether the current element of the iterator is acceptable.
   * @retval bool
   */
  public function accept() {
    $item  = $this->current();

    // Skip hidden files and directories.
    if ($item->getFilename()[0] === '.')
      return FALSE;

    if ($item->isDir())
      return TRUE;

    if ($item->getExtension() === "php") {
      $parents = class_parents(Helper\ClassHelper::getClass($item->getPathname()));
      return array_key_exists(self::ANCESTOR, $parents) ? TRUE : FALSE;
    }
    else
      return FALSE;
  }

}
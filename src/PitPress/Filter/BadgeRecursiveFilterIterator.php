<?php

/**
 * @file BadgeRecursiveFilterIterator.php
 * @brief This file contains the BadgeRecursiveFilterIterator class.
 * @details
 * @author Filippo F. Fadda
 */


//! PitPress filters namespace.
namespace PitPress\Filter;


use PitPress\Helper;


/**
 * @brief A custom filter to retrieves only the badges.
 * @details Badges are all the classes defined under the badges' namespace (and sub-namespaces), derived
 * from Gold, Silver or TBronze classes.
 * @nosubgrouping
 */
class BadgeRecursiveFilterIterator extends \RecursiveFilterIterator {

  #ifndef DOXYGEN_SHOULD_SKIP_THIS

  const ANCESTOR = 'PitPress\\Model\\Badge\\Badge';

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
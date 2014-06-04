<?php

/**
 * @file BadgeRecursiveFilterIterator.php
 * @brief This file contains the BadgeRecursiveFilterIterator class.
 * @details
 * @author Filippo F. Fadda
 */


//! PitPress filters namespace.
namespace PitPress\Filter;


use PitPress\Model\Badge\Badge;


/**
 * @brief
 * @details
 * @nosubgrouping
 */
class BadgeRecursiveFilterIterator extends \RecursiveFilterIterator {
  protected static $badges = ['Badge\Gold', 'Badge\Silver', 'Badge\Bronze'];


  public function accept() {
    $item  = $this->current();

    // Skip hidden files and directories.
    if ($item->getFilename()[0] === '.')
      return FALSE;

    if ($item->isDir())
      return TRUE;

    if ($item->getExtension() === "php") {
      $pathname = $item->getPathname();
      $class = preg_replace('/\.php\z/i', '', "\\".basename(str_replace("/", "\\", substr($pathname, stripos($pathname, "PitPress")))));

      //$class = basename($item->getFilename(), '.php');


      if (in_array(class_parents($class), static::$badges))
        return TRUE;

    }
    else
      return FALSE;
  }

}
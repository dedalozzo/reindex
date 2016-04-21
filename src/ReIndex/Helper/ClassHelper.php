<?php

/**
 * @file ClassHelper.php
 * @brief This file contains the ClassHelper class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Helper;


/**
 * @brief This helper class contains routines to handle classes.
 * @nosubgrouping
 */
class ClassHelper {


  /**
   * @brief Given a class path, it does return the class name even included its namespace.
   * @param[in] string $pathname The entire class path included its filename and extension.
   * @retval string The class name.
   */
  public static function getClass($pathname) {
    return preg_replace('/\.php\z/i', '', "\\".str_replace('/', "\\", substr($pathname, stripos($pathname, "ReIndex"))));
  }


  /**
   * @brief Given a class within its namespace, it returns the class name pruned by its namespace.
   * @param[in] string $class The class included its namespace.
   * @retval string The class name.
   */
  public static function getClassName($class) {
    return substr(strrchr($class, '\\'), 1);
  }


  /**
   * @brief Given a namespace, it returns the namespace itself pruned by its last part.
   * @param[in] string $namespace A namespace.
   * @retval string The namespace's root.
   */
  public static function getClassRoot($namespace) {
    if (preg_match('/^(.*[\\\\])/', $namespace, $matches))
      return $matches[0];
    else
      return "";
  }

}
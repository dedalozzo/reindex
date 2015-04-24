<?php

//! @file ArrayHelper.php
//! @brief This file contains the ArrayHelper class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Helper;


use EoC\Helper\ArrayHelper as ElephantOnCouchArrayHelper;


/**
 * @brief Helper with common array methods.
 * @nosubgrouping
 */
class ArrayHelper extends ElephantOnCouchArrayHelper {


  /**
   * @brief Returns a portion of the array.
   * @param[in] array $array The original array.
   * @param[in] int $number The number of elements from left to right.
   * @return array
   */
  public static function slice(array $array, $number = NULL) {
    return array_slice($array, 0, $number, TRUE);
  }


  /**
   * @brief Given a key, returns its related value.
   * @param[in] mixed $key A key.
   * @param[in] array $array The array to be searched.
   * @return int|bool The value or `false` in case the value doesn't exist.
   */
  public static function value($key, array $array) {

    if (array_key_exists($key, $array))
      return $array[$key];
    else
      return FALSE;
  }


  /**
   * @brief Modifies the specified array, depriving each ID of its related version.
   * @param[in|out] array $ids An array of IDs.
   */
  public static function unversion(array &$ids) {
    array_walk($ids, function(&$value, $key) {
        $value = strtok($value, Text::SEPARATOR);
      }
    );
  }


  /**
   * @brief Merge the two given arrays.
   * @details The returned array doesn't contain duplicate values
   * @param[in] array $array1 The first array.
   * @param[in] array $array2 The first array.
   * @return array
   */
  public static function merge(array $array1, array $array2) {
    $array = array_merge($array1, $array2);
    return array_keys(array_flip($array));
  }

} 
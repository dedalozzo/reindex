<?php

/**
 * @file Validation.php
 * @brief This file contains the Validation class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex;


use \Phalcon\Validation as PhalconValidation;


/**
 * @brief This class extends the Phalcon Validation, adding new methods.
 * @nosubgrouping
 */
class Validation extends PhalconValidation {


  /**
   * @brief Returns the first error message for the specified field.
   * @param string $field A field's name.
   * @return string
   */
  public function first($field) {
    $group = $this->getMessages();

    if (count($group) > 0) {
      $errors = $group->filter($field);
      $message = "";

      foreach ($errors as $error) {
        $message = $error->getMessage();
        break;
      }

      return $message;
    }
    else
      return "";
  }


  /**
   * @brief Returns the first error message for every validated field.
   * @return array An associative array of errors.
   */
  public function getFilteredMessages() {
    $errors = [];

    $group = $this->getMessages();

    foreach ($group as $msg) {
      $field = $msg->getField();

      if (!array_key_exists($field, $errors))
        $errors[$field] = [
          'code' => $msg->getCode(),
          'message' => $msg->getMessage(),
        ];
    }

    return $errors;
  }


  /**
   * @brief Validates the variables and raise an exception in case of failure.
   * @details It's like `validate`, but it raises an exception in case a field is invalid.
   * @param array $vars An associative array.
   */
  public function run(array $vars) {
    $group = $this->validate($vars);
    if (count($group) > 0) {
      throw new Exception\InvalidFieldException("Fields are incomplete or the entered values are invalid. The errors are reported in red under the respective entry fields.");
    }
  }

} 
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

} 
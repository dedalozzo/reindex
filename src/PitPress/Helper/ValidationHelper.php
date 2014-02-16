<?php

//! @file ValidationHelper.php
//! @brief This file contains the ValidationHelper class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Helper;


use Phalcon\Validation;


//! @brief This class extends the Phalcon Validation, adding new methods.
class ValidationHelper extends Validation {

  //! @brief Returns the first error message for the specified field.
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
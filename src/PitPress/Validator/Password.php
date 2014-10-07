<?php

//! @file Password.php
//! @brief This file contains the Password class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Validator;


use Phalcon\Validation;
use Phalcon\Validation\Validator;
use Phalcon\Validation\Message;


/**
 * @brief This class is used to validate a password.
 * @nosubgrouping
 */
class Password extends Validator implements Validation\ValidatorInterface {
  const PASSWORD_MIN_LENGTH = 8;
  const PASSWORD_MAX_LENGTH = 30;


  /**
   * @brief Executes the validation.
   * @param[in] Phalcon\Validation $validation
   * @param[in] string $attribute
   * @return bool
   */
  public function validate($validator, $attribute) {
    $value = $validator->getValue($attribute);

    if (empty($value))
      $message = "La password è obbligatoria.";
    elseif (strlen($value) < self::PASSWORD_MIN_LENGTH)
      $message = sprintf("La password deve contenere almeno %d caratteri.", self::PASSWORD_MIN_LENGTH);
    elseif (strlen($value) > self::PASSWORD_MAX_LENGTH)
      $message = sprintf("La password può contenere al massimo 20 caratteri.", self::PASSWORD_MAX_LENGTH);
    elseif (!preg_match("#[0-9]+#", $value))
      $message = "La password deve contenere almeno un numero.";
    elseif (!preg_match("#[a-zA-Z]+#", $value))
      $message = "La password deve contenere almeno una lettera.";
    else
      return TRUE;

    $validator->appendMessage(new Message($message, $attribute, 'Password'));

    return FALSE;
  }

} 
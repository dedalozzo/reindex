<?php

/**
 * @file Password.php
 * @brief This file contains the Password class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Validator;


use Phalcon\Validation;
use Phalcon\Validation\Validator;
use Phalcon\Validation\Message;


/**
 * @brief This class is used to validate a password.
 * @nosubgrouping
 */
class Password extends Validator implements Validation\ValidatorInterface {
  const MIN_LENGTH = 8;
  const MAX_LENGTH = 30;


  /**
   * @brief Executes the validation.
   * @param[in] Phalcon\Validation $validator An instance of a Phalcon validation component.
   * @param[in] string $attribute The attribute to be validated.
   * @retval bool
   */
  public function validate($validator, $attribute) {
    $value = $validator->getValue($attribute);

    if (empty($value))
      $message = "La password è obbligatoria.";
    elseif (strlen($value) < self::MIN_LENGTH)
      $message = sprintf("La password deve contenere almeno %d caratteri.", self::MIN_LENGTH);
    elseif (strlen($value) > self::MAX_LENGTH)
      $message = sprintf("La password può contenere al massimo %d caratteri.", self::MAX_LENGTH);
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
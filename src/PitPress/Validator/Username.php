<?php

//! @file Username.php
//! @brief This file contains the Username class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Validator;


use Phalcon\Validation;
use Phalcon\Validation\Validator;
use Phalcon\Validation\Message;


/**
 * @brief This class is used to validate a username.
 * @nosubgrouping
 */
class Username extends Validator implements Validation\ValidatorInterface {
  const MIN_LENGTH = 5;
  const MAX_LENGTH = 24;


  /**
   * @brief Executes the validation.
   * @param[in] Phalcon\Validation $validation
   * @param[in] string $attribute
   * @return bool
   */
  public function validate($validator, $attribute) {
    $value = $validator->getValue($attribute);

    if (empty($value))
      $message = "Il nome utente è obbligatorio.";
    elseif (mb_strlen($value, "UTF-8") < self::MIN_LENGTH)
      $message = sprintf("Il nome utente deve contenere almeno %d caratteri.", self::MIN_LENGTH);
    elseif (mb_strlen($value, "UTF-8") > self::MAX_LENGTH)
      $message = sprintf("Il nome utente può contenere al massimo %d caratteri.", self::MAX_LENGTH);
    elseif (!preg_match('/^[\p{L}\p{Mn}\p{Pd}\'\x{2019}]+$/iu', $value))
      $message = "Il nome utente non deve contenere caratteri speciali, numeri o spazi.";
    else
      return TRUE;

    $validator->appendMessage(new Message($message, $attribute, 'Username'));

    return FALSE;
  }

} 
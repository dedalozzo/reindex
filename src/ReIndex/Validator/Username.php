<?php

/**
 * @file Username.php
 * @brief This file contains the Username class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Validator;


use Phalcon\Validation;
use Phalcon\Validation\Validator;
use Phalcon\Validation\Message;
use Phalcon\DI;


/**
 * @brief This class is used to validate a username.
 * @nosubgrouping
 */
class Username extends Validator implements Validation\ValidatorInterface {
  const MIN_LENGTH = 5;
  const MAX_LENGTH = 24;

  private $di;
  private $guardian;


  public function __construct($options = NULL) {
    parent::__construct($options);
    $this->di = DI::getDefault();
    $this->guardian = $this->di['guardian'];
  }


  /**
   * @brief Executes the validation.
   * @details A username should match the following conditions:\n
   *   1. only one special char `.`, `_`, `-` are allowed and it must not be at the extremes of the string;
   *   2. the first character cannot be a number;
   *   3. all the other characters allowed are letters and numbers;
   *   4. the total length should be between 5 and 24 chars.
   * @param[in] Phalcon\Validation $validator An instance of a Phalcon validation component.
   * @param[in] string $attribute The attribute to be validated.
   * @retval bool
   */
  public function validate(\Phalcon\Validation $validation, $attribute) {
    $value = $validation->getValue($attribute);

    if (empty($value))
      $message = "Il nome utente è obbligatorio.";
    elseif (mb_strlen($value, "UTF-8") < self::MIN_LENGTH)
      $message = sprintf("Il nome utente deve contenere almeno %d caratteri.", self::MIN_LENGTH);
    elseif (mb_strlen($value, "UTF-8") > self::MAX_LENGTH)
      $message = sprintf("Il nome utente può contenere al massimo %d caratteri.", self::MAX_LENGTH);
    elseif (!preg_match('/(?=^.{3,20}$)^[a-zA-Z][a-zA-Z0-9]*[._-]?[a-zA-Z0-9]+$/', $value))
      $message = "Il nome utente deve cominciare con una lettera (maiuscola o minuscola), può contenere un solo
        carattere speciale (.-_) e questo carattere non deve essere all'inizio o alla fine della stringa, tutti gli altri
        caratteri permessi sono numeri e lettere.";
    elseif ($this->guardian->isTaken($value))
      $message = "Il nome utente non è disponibile.";
    else
      return TRUE;

    $validation->appendMessage(new Message($message, $attribute, 'Username'));

    return FALSE;
  }

} 
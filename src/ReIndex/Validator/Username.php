<?php

/**
 * @file Username.php
 * @brief This file contains the Username class.
 * @details
 * @author Filippo F. Fadda
 */


//! Classes to validate fields
namespace ReIndex\Validator;


use Phalcon\Validation;
use Phalcon\Validation\Validator;
use Phalcon\Validation\Message;
use Phalcon\Di;


/**
 * @brief This class is used to validate a username.
 * @nosubgrouping
 */
class Username extends Validator implements Validation\ValidatorInterface {
  const MIN_LENGTH = 5;
  const MAX_LENGTH = 24;

  private $di;
  private $config;
  private $guardian;

  private $minLength;
  private $maxLength;


  public function __construct($options = NULL) {
    parent::__construct($options);
    $this->di = Di::getDefault();
    $this->config = $this->di['config'];
    $this->guardian = $this->di['guardian'];

    $this->minLength = $this->config->application->usernameMinLength;
    $this->maxLength = $this->config->application->usernameMaxLength;
  }


  /**
   * @brief Executes the validation.
   * @details A username should match the following conditions:\n
   *   1. only one special char between `.`, `_`, `-` is allowed and it must not be at the extremes of the string;
   *   2. the first character cannot be a number;
   *   3. all the other characters allowed are letters and numbers;
   *   4. the total length should be between `usernameMinLength` and `usernameMaxLength` chars.
   * @param[in] Phalcon\Validation $validator An instance of a Phalcon validation component.
   * @param[in] string $attribute The attribute to be validated.
   * @retval bool
   */
  public function validate(\Phalcon\Validation $validation, $attribute) {
    $value = $validation->getValue($attribute);

    if (empty($value))
      $message = "Il nome utente è obbligatorio.";
    elseif (mb_strlen($value, "UTF-8") < $this->minLength)
      $message = sprintf("Il nome utente deve contenere almeno %d caratteri.", $this->minLength);
    elseif (mb_strlen($value, "UTF-8") > $this->maxLength)
      $message = sprintf("Il nome utente può contenere al massimo %d caratteri.", $this->maxLength);
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
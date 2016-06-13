<?php

/**
 * @file TagName.php
 * @brief This file contains the TagName class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Validator;


use Phalcon\Validation;
use Phalcon\Validation\Validator;
use Phalcon\Validation\Message;


/**
 * @brief This class is used to validate a tag's name.
 * @nosubgrouping
 */
class TagName extends Validator implements Validation\ValidatorInterface {
  const MIN_LENGTH = 1;
  const MAX_LENGTH = 25;

  # ^(.?[a-z0-9])+\.?#?\+{0,2}(-?[a-z0-9]+)*$
  #
  # Options: ^ and $ match at line breaks
  #
  # Assert position at the beginning of a line (at beginning of the string or after a line break character) «^»
  # Match the regular expression below and capture its match into backreference number 1 «(.?[a-zA-Z0-9])+»
  #    Between one and unlimited times, as many times as possible, giving back as needed (greedy) «+»
  #    Note: You repeated the capturing group itself.  The group will capture only the last iteration.  Put a capturing group around the repeated group to capture all iterations. «+»
  #    Match any single character that is not a line break character «.?»
  #       Between zero and one times, as many times as possible, giving back as needed (greedy) «?»
  #    Match a single character present in the list below «[a-zA-Z0-9]»
  #       A character in the range between “a” and “z” «a-z»
  #       A character in the range between “0” and “9” «0-9»
  # Match the character “.” literally «\.?»
  #    Between zero and one times, as many times as possible, giving back as needed (greedy) «?»
  # Match the character “#” literally «#?»
  #    Between zero and one times, as many times as possible, giving back as needed (greedy) «?»
  # Match the character “+” literally «\+{0,2}»
  #    Between zero and 2 times, as many times as possible, giving back as needed (greedy) «{0,2}»
  # Match the regular expression below and capture its match into backreference number 2 «(-?[a-zA-Z0-9]+)*»
  #    Between zero and unlimited times, as many times as possible, giving back as needed (greedy) «*»
  #    Note: You repeated the capturing group itself.  The group will capture only the last iteration.  Put a capturing group around the repeated group to capture all iterations. «*»
  #    Match the character “-” literally «-?»
  #       Between zero and one times, as many times as possible, giving back as needed (greedy) «?»
  #    Match a single character present in the list below «[a-zA-Z0-9]+»
  #       Between one and unlimited times, as many times as possible, giving back as needed (greedy) «+»
  #       A character in the range between “a” and “z” «a-z»
  #       A character in the range between “0” and “9” «0-9»
  # Assert position at the end of a line (at the end of the string or before a line break character) «$»
  const REGEX = '/^(.?[a-z0-9])+\.?#?\+{0,2}(-?[a-z0-9]+)*$/m';


  /**
   * @brief Executes the validation.
   * @param[in] Phalcon\Validation $validation An instance of a Phalcon validation component.
   * @param[in] string $attribute The attribute to be validated.
   * @retval bool
   */
  public function validate(\Phalcon\Validation $validation, $attribute) {
    $value = $validation->getValue($attribute);

    if (empty($value))
      $message = "La password è obbligatoria.";
    elseif (strlen($value) < self::MIN_LENGTH)
      $message = sprintf("Il nome di un tag deve contenere almeno %d carattere.", self::MIN_LENGTH);
    elseif (strlen($value) > self::MAX_LENGTH)
      $message = sprintf("La lunghezza di un tag non deve essere superiore a %d caratteri.", self::MAX_LENGTH);
    elseif (!preg_match(self::REGEX, $value))
      $message = "Il nome scelto per il tag contiene caratteri non validi.";
    else
      return TRUE;

    $validation->appendMessage(new Message($message, $attribute, 'Tag'));

    return FALSE;
  }

} 
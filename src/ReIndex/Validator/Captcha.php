<?php

/**
 * @file Captcha.php
 * @brief This file contains the Captcha class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Validator;


use Phalcon\Di;
use Phalcon\Validation;
use Phalcon\Validation\Validator;
use Phalcon\Validation\Message;


/**
 * @brief This class is used to validate a captcha.
 * @nosubgrouping
 */
class Captcha extends Validator implements Validation\ValidatorInterface {

  private $di;
  private $config;


  public function __construct($options = NULL) {
    parent::__construct($options);
    $this->di = Di::getDefault();
    $this->config = $this->di['config'];
  }

  /**
   * @brief  Uses reCAPTCHA to check is the user is not a robot.
   * @param[in] Phalcon\Validation $validation An instance of a Phalcon validation component.
   * @param[in] string $attribute The attribute to be validated.
   * @return bool
   */
  public function validate(\Phalcon\Validation $validation, $attribute) {
    $secret = $this->config->recaptcha->secret;

    $url = "https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$_POST['g-recaptcha-response']}&remoteip={$_SERVER["REMOTE_ADDR"]}";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16");
    $res = curl_exec($curl);
    curl_close($curl);

    if ($res === FALSE)
      throw new \RuntimeException('Internal server error. Please, try later.');

    $result = json_decode($res, TRUE)['success'];

    if (!$result)
      $validation->appendMessage(new Message('Please, prove that you are not a robot.', $attribute, 'Captcha'));

    return $result;
  }

}
<?php

/**
 * @file Question/AbstractPermission.php
 * @brief This file contains the AbstractPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post\Question;


use ReIndex\Security\Permission\AbstractPermission as Superclass;
use ReIndex\Doc\Question;


abstract class AbstractPermission extends Superclass {

  protected $question;


  /**
   * @brief Constructor.
   * @param[in] Doc::Question $question
   */
  public function __construct(Question $question) {
    $this->question = $question;
    parent::__construct();
  }

}
<?php

/**
 * @file Console.php
 * @brief This file contains the Console class.
 * @details
 * @author Filippo F. Fadda
 */


//! Command-Line Interpreter (CLI)
namespace ReIndex\Console;


use Symfony\Component\Console\Application;

use Phalcon\DiInterface;


/**
 * @brief This class extends the Application class of Symfony framework, with methods aim to set the Phalcon Dependency
 * Injector.
 */
final class Console extends Application {

  protected $_di;


  /**
   * @brief Sets the Phalcon Dependency Injector.
   * @param[in] DiInterface $di The Phalcon Dependency Injector object.
   */
  public function setDi(DiInterface $di) {
    $this->_di = $di;
  }


  /**
   * @brief Gets the Phalcon Dependency Injector.
   * @retval DiInterface
   */
  public function getDi() {
    return $this->_di;
  }

}
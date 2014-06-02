<?php

/**
 * @file Console.php
 * @brief This file contains the Console class.
 * @details
 * @author Filippo F. Fadda
 */


/**
 * @brief This namespace contains the Console class.
 */
namespace PitPress\Console;


use Symfony\Component\Console\Application;

use Phalcon\DiInterface;


/**
 * @brief This class extends the Application class of Symfony framework, with methods aim to set the Phalcon Dependency
 * Injector.
 */
class Console extends Application {

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
   * @return DiInterface
   */
  public function getDi() {
    return $this->_di;
  }

}
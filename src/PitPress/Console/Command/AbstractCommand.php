<?php

//! @file AbstractCommand.php
//! @brief This file contains the AbstractCommand class.
//! @details
//! @author Filippo F. Fadda


//! @brief This is the Commands namespace.
namespace PitPress\Console\Command;


use Phalcon\DI\InjectionAwareInterface;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Application;


//! @brief This class represents an abstract command that implements the InjectionAwareInterface to automatic set the
//! Phalcon Dependency Injector and make it available to every subclasses.
//! @nosubgrouping
class AbstractCommand extends Command implements InjectionAwareInterface {

  protected $_di;
  protected $logger;


  //! @brief Returns `true` in case `$arg` seems to be the string representation of an array, `false` otherwise.
  //! @param[in] $arg The command line argument.
  protected function isArray($arg) {
    if (preg_match('/\A[\[]([^\[\]]+)[\]]\z/i', $arg, $matches))
      return TRUE;
    else
      return FALSE;
  }


  //! @brief Returns `true` in case `$arg` is enclosed between paired delimiters ('' or ""), `false` otherwise.
  //! @details In case the argument is a string, paired delimiters are removed.
  //! @param[in|out] $arg The command line argument.
  protected function isString(&$arg) {
    if (preg_match('/\A[\'"]([^\'"]+)[\'"]\z/i', $arg, $matches)) {
      $arg = $matches[1];
      return true;
    }
    else
      return false;
  }


  //! @brief Casts the argument to the right format and jsonify it when necessary.
  //! @param[in] $arg The command line argument.
  //! @param[in] boolean $encode (optional) JSON encodes `$arg`.
  protected function castArg($arg, $encode = TRUE) {
    if ($this->isArray($arg))
      return $arg;
    elseif ($this->isString($arg)) {
      if ($encode)
        return json_encode($arg);
      else
        return $arg;
    }
    else
      return $arg + 0;
  }


  //! @brief Overrides this method to set the Dependency Injector.
  public function setApplication(Application $application = null) {
    parent::setApplication($application);

    if ($application)
      $this->setDi($application->getDi());
  }


  //! @brief Sets the Dependency Injector.
  public function setDi($di) {
    $this->_di = $di;
    $this->logger = $this->_di['logger'];
  }


  //! @brief Gets the Dependency Injector.
  public function getDi() {
    return $this->_di;
  }

}
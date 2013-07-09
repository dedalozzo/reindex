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


  //! @brief Overrides this method to set the Dependency Injector.
  public function setApplication(Application $application = null) {
    parent::setApplication($application);

    if ($application)
      $this->setDi($application->getDi());
  }


  //! @brief Sets the Dependency Injector.

  public function setDi($di) {
    $this->_di = $di;
  }


  //! @brief Gets the Dependency Injector.
  public function getDi() {
    return $this->_di;
  }

}
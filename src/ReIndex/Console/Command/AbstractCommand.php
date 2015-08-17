<?php

/**
 * @file AbstractCommand.php
 * @brief This file contains the AbstractCommand class.
 * @details
 * @author Filippo F. Fadda
 */


//! This is the Commands namespace.
namespace ReIndex\Console\Command;


use Phalcon\DI\InjectionAwareInterface;
use Phalcon\DiInterface;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use EoC\Helper\TimeHelper;


/**
 * @brief This class represents an abstract command that implements the InjectionAwareInterface to automatic set the
 * Phalcon Dependency Injector and make it available to every subclasses.
 * @nosubgrouping
 */
abstract class AbstractCommand extends Command implements InjectionAwareInterface {

  protected $di;
  protected $log;

  protected $start;


  /**
   * @brief Creates an instance of the command.
   */
  public function __construct() {
    parent::__construct();

    $this->start = time();
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $time = TimeHelper::since($this->start);
    $output->writeln(PHP_EOL.sprintf("%d days, %d hours, %d minutes, %d seconds", $time['days'], $time['hours'], $time['minutes'], $time['seconds']));
  }


  /**
   * @brief Overrides this method to set the Dependency Injector.
   * @param[in] Symfony::Component::Console::Application $application Symfony console application instance.
   */
  public function setApplication(Application $application = NULL) {
    parent::setApplication($application);

    if ($application)
      $this->setDi($application->getDi());
  }


  /**
   * @brief Sets the Dependency Injector.
   * @param[in] Phalcon::DiInterface $di Phalcon Dependency Injection Interface
   */
  public function setDi(DiInterface $di) {
    $this->di = $di;
    $this->log = $this->di['log'];
  }


  /**
   * @brief Gets the Dependency Injector.
   * @retval Phalcon::DiInterface
   */
  public function getDi() {
    return $this->di;
  }

}
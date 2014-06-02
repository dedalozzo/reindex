<?php

/**
 * @file MainTask.php
 * @brief This file contains the MainTask class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Task;


use Phalcon\CLI\Task;


class MainTask extends Task {

  public function initialize() {

  }

  // Default action.
  public function mainAction() {
    // todo show the help

    echo "mainAction() of MainTask".PHP_EOL;
  }

} 
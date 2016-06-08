<?php

/**
 * @file ITask.php
 * @brief This file contains the ITask class.
 * @details
 * @author Filippo F. Fadda
 */


//! Tasks
namespace ReIndex\Queue\Task;


/**
 * @brief A common interface to describe a generic task.
 */
interface ITask {


  /**
   * @brief Performs the task execution.
   */
  function execute();

}
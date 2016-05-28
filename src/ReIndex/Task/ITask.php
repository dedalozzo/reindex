<?php

/**
 * @file ITask.php
 * @brief This file contains the ITask interface.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Task;


/**
 * @brief A piece of work to be done or undertaken.
 * @nosubgrouping
 */
interface ITask {


  /**
   * @brief Executes the task.
   */
  function execute();

}
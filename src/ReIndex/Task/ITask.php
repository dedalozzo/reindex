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
interface ITask extends \Serializable {


  /**
   * @brief Initializes the task.
   */
  function init();


  /**
   * @brief Executes the task.
   */
  function execute();

}
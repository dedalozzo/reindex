<?php

/**
 * @file TaskCollection.php
 * @brief This file contains the TaskCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Task\ITask;
use ReIndex\Queue\TaskQueue;


/**
 * @brief This class is used to represent a collection of tasks.
 * @nosubgrouping
 */
final class TaskCollection extends MetaCollection {

  /**
   * @var TaskQueue $queue
   */
  protected $queue;


  /**
   * @brief Creates a new collection of tasks.
   * @param[in] string $name Collection's name.
   * @param[in] array $meta Array of metadata.
   */
  public function __construct($name, array &$meta) {
    parent::__construct($name, $meta);

    $this->queue = $this->di['taskqueue'];
  }


  /**
   * @brief Adds the specified task to the tasks collection and enqueues it.
   * @param[in] ITask $task The task object.
   */
  public function add(ITask $task) {
    $this->queue->add($task);

    // Adds the task to the collection in case is not present.
    if ($this->exists($task))
      return;
    else
      $this->meta[$this->name][get_class($task)] = NULL;
  }


  /**
   * @brief Removes the specified task from the collection.
   * @param[in] ITask $task The task object.
   */
  public function remove(ITask $task) {
    if ($this->exists($task))
      unset($this->meta[$this->name][get_class($task)]);
  }


  /**
   * @brief Returns `true` if the task is already present, `false` otherwise.
   * @param[in] ITask $task A task object.
   * @retval bool
   */
  public function exists(ITask $task) {
    return isset($this->meta[$this->name][get_class($task)]);
  }


}
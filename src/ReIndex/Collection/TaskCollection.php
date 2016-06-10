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
class TaskCollection extends MetaCollection {

  const NAME = "tasks";

  /**
   * @var TaskQueue $queue
   */
  protected $queue;


  /**
   * @brief Creates a new collection of tags.
   * @param[in] array $meta Post's array of metadata.
   */
  public function __construct(array &$meta) {
    parent::__construct($meta);

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
      $this->meta[static::NAME][get_class($task)] = NULL;
  }


  /**
   * @brief Removes the specified task from the collection.
   * @param[in] ITask $task The task object.
   */
  public function remove(ITask $task) {
    if ($this->exists($task))
      unset($this->meta[static::NAME][get_class($task)]);
  }


  /**
   * @brief Returns `true` if the task is already present, `false` otherwise.
   * @param[in] ITask $task A task object.
   * @retval bool
   */
  public function exists(ITask $task) {
    return isset($this->meta[static::NAME][get_class($task)]);
  }


}
<?php

/**
 * @file SynonymCollection.php
 * @brief This file contains the SynonymCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Task\ITask;
use ReIndex\Queue\TaskQueue;
use ReIndex\Helper;


/**
 * @brief This class is used to represent a collection of synonyms. It's used to manage the synonyms of any tag.
 * @nosubgrouping
 */
class SynonymCollection extends MetaCollection {

  const NAME = "synonyms";

  /**
   * @var TaskQueue $queue
   */
  protected $queue;


  /**
   * @brief Creates a new collection of synonyms.
   * @param[in] array $meta Tag's array of metadata.
   */
  public function __construct(array &$meta) {
    parent::__construct($meta);

    $this->queue = $this->di['taskqueue'];
  }


  private function addMultipleAtOnce(array $synonyms) {
    $this->meta['synonyms'] = Helper\ArrayHelper::merge($this->meta['synonyms'], $synonyms);
  }


  /**
   * @brief Returns the ids of its synonyms.
   * @retval array An array of strings.
   */
  public function getSynonyms() {
    return (!$this->isSynonym()) ? $this->meta['synonyms'] : [];
  }


  /**
   * @brief Removes all the synonyms from the collection.
   */
  public function reset() {
    $this->meta['synonyms'] = [];
  }


  /**
   * @brief Marks the provided tag as synonym of the current tag.
   * @param[in] Tag $tag The tag you want add as synonym to the current tag.
   */
  public function add(Tag $tag) {
    // You can't add a synonym to a synonym, neither you can add a master to a synonym.
    if ($this->isSynonym() or !$this->state->isCurrent() or $tag->isSynonym() or !$tag->state->isCurrent())
      throw new \RuntimeException("You can't add a synonym to a synonym, neither you can add a master to a synonym.");

    array_push($this->meta['synonyms'], $tag->unversionId);
    $this->addMultipleAtOnce($tag->getSynonyms());

    $tag->transIntoSynonym();
    $tag->save();

    // In order to execute a command have have it not hang your php script while it runs, the program you run must not
    // output back to php. To do this, redirect both stdout and stderr to /dev/null, then background it.
    // @see http://stackoverflow.com/a/3819422/1889828
    $cmd = 'nohup rei synonym --add='. $tag->id . ' -f > /dev/null 2>&1 &';

    exec($cmd);
  }


  /**
   * @brief Removes the specified task from the collection.
   * @param[in] ITask $task The task object.
   */
  public function remove(Tag $tag) {
    if ($this->exists($task))
      unset($this->meta[static::NAME][get_class($task)]);
  }


  /**
   * @brief Returns `true` if the task is already present, `false` otherwise.
   * @param[in] ITask $task A task object.
   * @retval bool
   */
  public function exists(Tag $tag) {
    return isset($this->meta[static::NAME][get_class($task)]);
  }


}
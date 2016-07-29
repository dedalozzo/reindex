<?php

/**
 * @file TaskQueue.php
 * @brief This file contains the TaskQueue class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Queue;


use ReIndex\Task\ITask;

use AMQPChannel;
use AMQPExchange;
use AMQPQueue;
use AMQPEnvelope;


/**
 * @brief A special queue to handle tasks.
 */
class TaskQueue extends AbstractQueue {

  const ROUTING_KEY = 'task_queue';

  protected $rabbit;
  protected $channel;
  protected $queue;


  public function __construct($config) {
    parent::__construct($config);

    // Creates the channel.
    $this->channel = new AMQPChannel($this->amqp);

    // It doesn't prefetch messages, since the tasks can be time consuming we want execute them sequentially.
    $this->channel->qos(0, 1);

    // Declares the queue.
    $this->queue = new AMQPQueue($this->channel);
    $this->queue->setName(static::ROUTING_KEY);
    $this->queue->setFlags(AMQP_DURABLE);
    $this->queue->declareQueue();
  }


  public function __destruct() {
    parent::__destruct();
  }


  /**
   * @brief Adds a task to the queue.
   * @param[in] ITask $task A task.
   */
  public function add(ITask $task) {
    // The exchange is used to publish a message on the queue.
    $exchange = new AMQPExchange($this->channel);
    $exchange->publish(serialize($task), static::ROUTING_KEY);
  }


  /**
   * @brief Performs the execution of the next task in the queue.
   */
  public function perform() {
    $jobsCounter = $this->config->application->maxJobs;

    $callback = function(AMQPEnvelope $msg, AMQPQueue $queue) use (&$jobsCounter) {
      // Creates a new task.
      $task = unserialize($msg->getBody());

      try {
        // Executes the task.
        $task->execute();

        // Acknowledges the receipt of the message.
        $queue->ack($msg->getDeliveryTag());
      }
      catch (\Exception $e) {
        print_r($e);
        $queue->nack($msg->getDeliveryTag(), AMQP_REQUEUE);
      }

      // To avoid long running processes and consequently memory leaks,
      // the worker consumes at most N messages, then exit.
      if ($jobsCounter > 1) {
        $jobsCounter--;

        // Returns `true` to consume the next message.
        return TRUE;
      }
      else {
        // Forces the consume() method to exit.
        return FALSE;
      }

    };

    $this->queue->consume($callback);
  }

}
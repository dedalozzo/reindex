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

use Phalcon\Di;


/**
 * @brief A special queue to  interface to describe a generic task.
 */
class TaskQueue extends AbstractQueue {

  const ROUTING_KEY = 'task_queue';

  protected $rabbit;
  protected $channel;
  protected $queue;


  public function __construct() {
    parent::__construct();

    // Creates the channel.
    $this->channel = new AMQPChannel($this->amqp);
    $this->channel->setPrefetchCount(1);

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

    $callback = function(AMQPEnvelope $msg, AMQPQueue $queue) use (&$max_jobs) {
      $task = unserialize($msg->getBody());
      $task->execute();
      $queue->ack($msg->getDeliveryTag());
    };

    $this->queue->consume($callback);
  }

}
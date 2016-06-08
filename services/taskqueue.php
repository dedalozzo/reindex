<?php

/**
 * @file taskqueue.php
 * @brief Creates a task queue, using the RabbitMQ client.
 * @details
 * @author Filippo F. Fadda
 * @see https://github.com/rabbitmq/rabbitmq-tutorials/tree/master/php-amqp
 */


use ReIndex\Queue\TaskQueue;


$di->setShared('taskqueue',
  function() use ($config) {
    $queue = new TaskQueue($config);

    return $queue;
  }
);
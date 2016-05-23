<?php

/**
 * @file rabbitmq.php
 * @brief Establishes a connection to RabbitMQ.
 * @details
 * @author Filippo F. Fadda
 */


// Creates an instance of EoC client and return it.
$di->setShared('rabbitmq',
  function() use ($config) {
    $rabbit = AMQPConnection([
      'host' => $config->rabbitmq->host,
      'port' => $config->rabbitmq->port,
      'login' => $config->rabbitmq->user,
      'password' => $config->rabbitmq->password
      ]
    );

    $rabbit->connect();

    return $rabbit;
  }
);
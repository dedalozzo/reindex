<?php

/**
 * @file AbstractQueue.php
 * @brief This file contains the AbstractQueue class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Queue;


use AMQPConnection;

use Phalcon\Di;


abstract class AbstractQueueAdapter implements IQueueAdapter {
  protected $amqp;


  public function __construct() {
    $di = Di::getDefault();
    $config = $di['config'];

    $this->amqp = new AMQPConnection();
    $this->amqp->setHost($config->rabbitmq->host);
    $this->amqp->setPort($config->rabbitmq->port);
    $this->amqp->setLogin($config->rabbitmq->user);
    $this->amqp->setPassword($config->rabbitmq->password);

    $this->amqp->connect();
  }


  abstract public function publish($msg = NULL);


  abstract public function consume();


  public function __destruct() {
    $this->amqp->disconnect();
  }


}
<?php

/**
 * @file AbstractQueue.php
 * @brief This file contains the AbstractQueue class.
 * @details
 * @author Filippo F. Fadda
 */


//! AMQP queue related classes
namespace ReIndex\Queue;


use AMQPConnection;

use Phalcon\Di;


/**
 * @brief An abstract AMQP queue.
 */
abstract class AbstractQueue {
  protected $amqp;


  /**
   * @brief Constructor.
   */
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


  /**
   * @brief Destructor.
   */
  public function __destruct() {
    $this->amqp->disconnect();
  }

}
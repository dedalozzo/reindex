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

use Phalcon\Config;
use Phalcon\Di;


/**
 * @brief An abstract AMQP queue.
 */
abstract class AbstractQueue {

  /**
   * @var AMQPConnection $amqp
   */
  protected $amqp;

  /**
   * @var \Phalcon\Config $config
   */
  protected $config;

  /**
   * @var Logger $log
   */
  protected $log;


  /**
   * @brief Constructor.
   * @param[in] \Phalcon\Config $config The configuration object.
   * @param[in] \Phalcon\Di $di The Dependency Injection component.
   */
  public function __construct(Config $config, Di $di) {
    $this->config = $config;
    $this->log = $di['log'];

    $this->amqp = new AMQPConnection();

    $this->amqp->setHost($config->rabbitmq->host);
    $this->amqp->setPort((int)$config->rabbitmq->port);
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
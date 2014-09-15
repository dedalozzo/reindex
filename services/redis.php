<?php

/**
 * @file redis.php
 * @brief Establishes a connection to Redis.
 * @details
 * @author Filippo F. Fadda
 */


// Creates an instance of Redis client and return it.
$di->setShared('redis',
  function() use ($config) {
    $redis = new Redis();
    //$redis->pconnect($config->redis->socket);
    $redis->pconnect($config->redis->host, $config->redis->port);

    return $redis;
  }
);
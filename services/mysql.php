<?php

/**
 * @file mysql.php
 * @brief Establishes a connection to MySQL.
 * @details
 * @author Filippo F. Fadda
 */


// Creates an instance of MySQL client and return it.
$di->setShared('mysql',
  function() use ($config) {
    $mysql = mysqli_connect($config->mysql->host, $config->mysql->user, $config->mysql->password) or die(mysqli_error($mysql));
    mysqli_select_db($mysql, $config->mysql->database) or die(mysql_error($mysql));

    return $mysql;
  }
);
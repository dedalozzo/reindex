<?php

//! @file mysql.php
//! @brief Establishes a connection to MySQL.
//! @details
//! @author Filippo F. Fadda


// Creates an instance of MySQL client and return it.
$di->setShared('mysql',
  function() use ($config) {
    $mysql = mysql_connect('localhost', $config->mysql->user, $config->mysql->password) or die(mysql_error());
    mysql_select_db($config->mysql->database) or die(mysql_error());

    return $mysql;
  }
);
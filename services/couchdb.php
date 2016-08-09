<?php

/**
 * @file couchdb.php
 * @brief Establishes a connection to CouchDB.
 * @details
 * @author Filippo F. Fadda
 */


use EoC\Couch;
use EoC\Adapter;


// Creates an instance of EoC client and return it.
$di->setShared('couchdb',
  function() use ($config) {
    $cf = &$config['couchdb'];

    $couch = new Couch(new Adapter\SocketAdapter($cf['host'].":".$cf['port'], $cf['user'], $cf['password']));
    //$couch = new Couch(new Adapter\CurlAdapter($cf['host'].":".$cf['port'], $cf['user'], $cf['password']));

    $couch->setDbPrefix($config['application']['dbPrefix']);

    return $couch;
  }
);
<?php

/**
 * @file couchdb.php
 * @brief Establishes a connection to CouchDB.
 * @details
 * @author Filippo F. Fadda
 */


use ElephantOnCouch\Couch;
use ElephantOnCouch\Adapter;


// Creates an instance of ElephantOnCouch client and return it.
$di->setShared('couchdb',
  function() use ($config) {
    //$couch = new Couch(new Adapter\NativeAdapter($config->couchdb->host.":".$config->couchdb->port, $config->couchdb->user, $config->couchdb->password));
    $couch = new Couch(new Adapter\CurlAdapter($config->couchdb->host.":".$config->couchdb->port, $config->couchdb->user, $config->couchdb->password));

    $couch->selectDb($config->couchdb->database);

    return $couch;
  }
);
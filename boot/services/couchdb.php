<?php

//! @file couchdb.php
//! @brief Establishes a connection to CouchDB.
//! @details
//! @author Filippo F. Fadda


use ElephantOnCouch\ElephantOnCouch;


// Creates an instance of ElephantOnCouch client and return it.
$di->setShared('couchdb',
  function() use ($config) {
    $couch = new ElephantOnCouch(ElephantOnCouch::DEFAULT_SERVER, $config->couchdb->user, $config->couchdb->password);

    $couch->useCurl();

    $couch->selectDb($config->couchdb->database);

    return $couch;
  }
);
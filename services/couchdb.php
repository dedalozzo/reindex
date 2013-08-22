<?php

//! @file couchdb.php
//! @brief Establishes a connection to CouchDB.
//! @details
//! @author Filippo F. Fadda


use ElephantOnCouch\Couch;


// Creates an instance of ElephantOnCouch client and return it.
$di->setShared('couchdb',
  function() use ($config) {
    $couch = new Couch($config->couchdb->ipAddress.":".$config->couchdb->port, $config->couchdb->user, $config->couchdb->password);

    $couch->selectDb($config->couchdb->database);

    return $couch;
  }
);
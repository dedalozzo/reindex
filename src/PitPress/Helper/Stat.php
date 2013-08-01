<?php

//! @file Stat.php
//! @brief This file contains the Stat class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Helper;


use Phalcon\DI;


//! @brief Provides static methods to generate statistics.
class Stat {
  protected $di; // Stores the default Dependency Injector.
  protected $couch; // Stores the ElephantOnCouch client instance.
  protected $redis; // Stores the Redis client instance.


  //! @brief Constructor.
  public function __construct() {
    $this->di = DI::getDefault();
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];
  }


  public function getUpdatesCount() {
    $result = $this->couch->queryView("posts", "allLatest")->getBodyAsArray();

    if (empty($result['rows']))
      return 0;
    else
      return number_format($result['rows'][0]['value'], 0, ",", ".");
  }

} 
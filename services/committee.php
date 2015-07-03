<?php

/**
 * @file committee.php
 * @brief Creates the committee instance.
 * @details
 * @author Filippo F. Fadda
 */


use PitPress\Mediator\Committee;


// Creates an instance of Committee and return it.
$di->setShared('committee',
  function() use ($di, $root) {
    $committee = new Committee($di, $root."/src/PitPress/Model/Badge/");

    return $committee;
  }
);
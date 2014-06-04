<?php

/**
 * @file badgeloader.php
 * @brief Loads the badges list.
 * @details
 * @author Filippo F. Fadda
 */


use PitPress\Loader\BadgeLoader;


// Creates an instance of BadgeLoader and return it.
$di->setShared('badgeloader',
  function() use ($root) {

    $loader = new BadgeLoader($root."/src/PitPress/Model/Badge/");

    return $loader;
  }
);
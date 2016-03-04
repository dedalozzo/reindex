<?php

/**
 * @file github.php
 * @brief Creates a GitHub client to use its APIs.
 * @details
 * @author Filippo F. Fadda
 */


// Creates a GitHub client to use its APIs.
$di->setShared('github',
  function() use ($root, $config) {
    $client = new Github\HttpClient\CachedHttpClient();

    $client->setCache(
      // Built in one, or any cache implementing this interface:
      // Github\HttpClient\Cache\CacheInterface
      new Github\HttpClient\Cache\FilesystemCache($root.'/'.$config->application->cacheDir.'github/')
    );

    return $client;
  }
);
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
    $github = new Github\HttpClient\CachedHttpClient();
    $github->setCache(
      // Uses the built-in one, or any cache implementing this interface: Github\HttpClient\Cache\CacheInterface.
      new Github\HttpClient\Cache\FilesystemCache($root.'/'.$config->application->cacheDir.'github/')
    );

    $github = new Github\Client($github);
    $github->authenticate($config->github->key, $config->github->secret, Github\Client::AUTH_URL_CLIENT_ID);

    return $github;
  }
);
<?php

//! @file OAuth2Consumer.php
//! @brief This file contains the OAuth2Consumer class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Security\Consumer;

use OAuth\Common\Http\Uri\UriFactory;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;
use OAuth\ServiceFactory;


abstract class OAuth2Consumer {

  protected $meta;
  protected $service;
  protected $token = NULL;


  /**
   * @brief Connects to the service provider and request an access token.
   */
  public function __costruct() {
    $this->initialize();

    if (empty($_GET['code']))
      $this->showAuthorizationForm();
    else
      $this->requestAccessToken();
  }


  /**
   * @brief Initializes the consumer.
   */
  protected function initialize() {
    $uri = (new UriFactory())->createFromSuperGlobalArray($_SERVER);
    $uri->setQuery('');

    $storage = new Session();

    $credentials = new Credentials(
      $this->di['config'][$this->getName()]['key'],
      $this->di['config'][$this->getName()]['secret'],
      $this->uri->getAbsoluteUri()
    );

    $this->service = (new ServiceFactory())->createService($this->getName(), $credentials, $storage, $this->getScope());
  }


  /**
   * @brief Redirects to service provider authorization form.
   */
  protected function showAuthorizationForm() {
    // State is used to prevent CSRF, it's required.
    $uri = $this->service->getAuthorizationUri(['state' => 'DCEEFWF45453sdffef424']);
    header('Location: '.$uri);
  }


  /**
   * @brief Requests for an access token.
   */
  protected function requestAccessToken() {
    $this->token = $this->service->requestAccessToken($_GET['code']);
  }


  /**
   * @brief Sends a request to the service provider.
   */
  protected function request($url) {
    // Send a request with it. Please note that XML is the default format.
    //'/people/~:(id,first-name,last-name,email-address,headline,summary)?format=json'
    $this->meta = json_decode($this->service->request($url), TRUE);
  }

}
<?php

//! @file AuthController.php
//! @brief This file contains the AuthController class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use OAuth\Common\Http\Uri\UriFactory;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials; 
use OAuth\ServiceFactory;


//! @brief Controller of Auth actions.
//! @nosubgrouping
class AuthController extends BaseController {


  public function facebookAction($params) {
    $uriFactory = new UriFactory();
    $currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
    $currentUri->setQuery('');

    $serviceFactory = new ServiceFactory();

    // Session storage
    $storage = new Session();

    // Setup the credentials for the requests
    $credentials = new Credentials(
      '466134446840960',
      'a4b4fb1547097f719adb5df34d5f5fcb',
      $currentUri->getAbsoluteUri()
    );

    $facebookService = $serviceFactory->createService('facebook', $credentials, $storage, []);

    if (!empty($_GET['code'])) {
      // This was a callback request from facebook, get the token
      $token = $facebookService->requestAccessToken($_GET['code']);

      // Send a request with it
      $result = json_decode($facebookService->request('/me'), true);

      // Show some of the resultant data
      echo 'Your unique facebook user id is: ' . $result['id'] . ' and your name is ' . $result['name'];

    }
    else {
      $url = $facebookService->getAuthorizationUri();
      header('Location: ' . $url);
    }
  }


  public function googleAction($params) {
    $uriFactory = new UriFactory();
    $currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
    $currentUri->setQuery('');

    $serviceFactory = new ServiceFactory();

    
    // Session storage
    $storage = new Session();

    // Setup the credentials for the requests
    $credentials = new Credentials(
      '970273883225.apps.googleusercontent.com',
      'AtD1DJtRslD6ixNDX5sTRoGM',
      $currentUri->getAbsoluteUri()
    );

    $googleService = $serviceFactory->createService('google', $credentials, $storage, ['userinfo_email', 'userinfo_profile']);

    if (!empty($_GET['code'])) {
      // This was a callback request from google, get the token
      $googleService->requestAccessToken($_GET['code']);

      // Send a request with it
      $result = json_decode($googleService->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);

      // Show some of the resultant data
      echo 'Your unique google user id is: ' . $result['id'] . ' and your name is ' . $result['name'];

    }
    else {
      $url = $googleService->getAuthorizationUri();
      header('Location: ' . $url);
    }

  }


  public function linkedinAction($params) {
    $uriFactory = new UriFactory();
    $currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
    $currentUri->setQuery('');

    $serviceFactory = new ServiceFactory();

    $storage = new Session();

    // Sets the credentials for the requests.
    $credentials = new Credentials(
      '774i5aagny4ryw',
      'j9bDvXpnyYJtxLBC',
      $currentUri->getAbsoluteUri()
    );

    $linkedinService = $serviceFactory->createService('linkedin', $credentials, $storage, ['r_basicprofile']);

    if (!empty($_GET['code'])) {
      // This was a callback request from linkedin, get the token
      $token = $linkedinService->requestAccessToken($_GET['code']);

      // Send a request with it. Please note that XML is the default format.
      $result = json_decode($linkedinService->request('/people/~?format=json'), true);

      // Show some of the resultant data
      echo 'Your linkedin first name is ' . $result['firstName'] . ' and your last name is ' . $result['lastName'];

    }
    else {
      // state is used to prevent CSRF, it's required
      $url = $linkedinService->getAuthorizationUri(['state' => 'DCEEFWF45453sdffef424']);
      header('Location: ' . $url);
    }

  }


  public function githubAction($params) {
    $uriFactory = new UriFactory();
    $currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
    $currentUri->setQuery('');

    $serviceFactory = new ServiceFactory();


    // Session storage
    $storage = new Session();

    // Setup the credentials for the requests
    $credentials = new Credentials(
      '5c4b7a60d8a624257632',
      '4bcd0beccd6f1a69992b9d2181142cdf2ccaa697',
      $currentUri->getAbsoluteUri()
    );

    $gitHub = $serviceFactory->createService('GitHub', $credentials, $storage, ['user']);

    if (!empty($_GET['code'])) {
      // This was a callback request from github, get the token
      $gitHub->requestAccessToken($_GET['code']);

      $result = json_decode($gitHub->request('user/emails'), true);

      echo 'The first email on your github account is ' . $result[0];

    }
    else {
      $url = $gitHub->getAuthorizationUri();
      header('Location: ' . $url);
    }

  }

}
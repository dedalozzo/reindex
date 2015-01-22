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

use Phalcon\DI;
use Phalcon\Validation\Validator\PresenceOf;

use PitPress\Model\User;
use PitPress\Factory\UserFactory;
use PitPress\Helper\Cookie;
use PitPress\Helper\ValidationHelper;
use PitPress\Exception\InvalidFieldException;


/**
 * @brief Ancestor class of each OAuth2 consumer class.
 * @nosubgrouping
 */
abstract class OAuth2Consumer {

  protected $di; // Stores the default Dependency Injector.
  protected $user; // Stores the current user.
  protected $service; // Stores the service used to connect to the provider.
  protected $token; // Stores the user token.


  /**
   * @brief Connects to the service provider and request an access token.
   */
  public function __costruct() {
    $this->di = DI::getDefault();
    $this->user = $this->di['guardian']->getUser();

    $this->initialize();

    if (empty($_GET['code']))
      $this->showAuthorizationForm();
    else
      $this->requestAccessToken();
  }


  /**
   * @brief Initializes the consumer.
   */
  private function initialize() {
    $uri = (new UriFactory())->createFromSuperGlobalArray($_SERVER);
    $uri->setQuery('');

    $storage = new Session();

    $credentials = new Credentials(
      $this->di['config'][$this->getName()]['key'],
      $this->di['config'][$this->getName()]['secret'],
      $uri->getAbsoluteUri()
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
  protected function fetch($url) {
    return json_decode($this->service->request($url), TRUE);
  }


  /**
   * @brief Performs the sign in.
   * @param[in] User $user The user instance.
   * @param[in] array $userData An associative array with the user information.
   */
  private function signIn(User $user, array $userData) {
    $this->update($user, $userData);
    Cookie::set($user);
  }


  /**
   * @brief Performs the sign up.
   * @param[in] array $userData An associative array with the user information.
   */
  private function signUp(array $userData) {
    $user = User::create();
    $this->update($user, $userData);
    Cookie::set($user);
  }


  /**
   * @brief Validates mandatory properties: id and email.
   * @details Each provider uses a different associative key to identify the user id and his own email.
   * @param[in] string $assocKeyForId The associative key for the id value.
   * @param[in] string $assocKeyForEmail The associative key for the email value.
   * @param[in] array $userData An associative array with the user information.
   */
  protected function validate($assocKeyForId, $assocKeyForEmail, array $userData) {
    $validation = new ValidationHelper();
    $validation->add($assocKeyForId, new PresenceOf(["message" => "L'id è obbligatorio."]));
    $validation->add($assocKeyForEmail, new PresenceOf(["message" => "L'e-mail è obbligatoria."]));

    $group = $validation->validate($userData);
    if (count($group) > 0) {
      throw new InvalidFieldException("Le informazioni fornite da LinkedIn sono incomplete.");
    }
  }


  /**
   * @brief Process the user data and his connections.
   * @details In case the current user is a guest, checks if any user is registered with the `$userId`. If any exists,
   * it performs the sign in, otherwise sign up the new user.
   * @param[in] string $userId The user identifier used by the provider.
   * @param[in] string $userEmail The user email.
   * @param[in] array $userData An associative array with the user information.
   * @param[in] array $userConnections An associative array with the user information.
   */
  protected function process($userId, $userEmail, array $userData, array $userConnecions = []) {
    // Searches for the user associated with `$userId` or `$userEmail`.
    $user = UserFactory::fromLogin($this->getName(), $userId);
    if ($user->isGuest()) $user = UserFactory::fromEmail($userEmail);

    if ($this->user->isGuest()) {
      if ($user->isMember())
        $this->signIn($user, $userData);
      else {
        if ($user->isGuest())
          $this->signUp($userData);
        else
          $this->signIn($user, $userData);
      }
    }
    else {
      if ($this->user->match($user->id))
        $this->update($this->user, $userData);
      else
        throw new UserMismatchException(sprintf("Il tuo account %s è già associato ad un'altra utenza. Contatta il supporto tecnico all'indirizzo %s", ucfirst($this->getName(), $this->di['config']['application']['supportEmail'])));
    }
  }


  /**
   * @brief Updates the user object using the provided data.
   * @param[in] User $user The user instance.
   * @param[in] array $data An associative array with the user information.
   */
  abstract protected function update(User $user, array $userData);


  /**
   * @brief Consumes the user information.
   */
  abstract public function consume();


  /**
   * @brief Returns the consumer name.
   * @return string
   */
  abstract public function getName();


  /**
   * @brief Returns the data scope.
   * @return array
   */
  abstract public function getScope();

}
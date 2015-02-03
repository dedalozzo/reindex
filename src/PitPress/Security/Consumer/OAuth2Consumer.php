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
use PitPress\Exception;


/**
 * @brief Ancestor class of each OAuth2 consumer class.
 * @nosubgrouping
 */
abstract class OAuth2Consumer {

  /** @name Field Names */
  //!@{
  const ID = 'id';
  const EMAIL = 'email';
  const PROFILE_URL = 'profile_url';
  //!@}

  protected $di; // Stores the default Dependency Injector.
  protected $user; // Stores the current user.
  protected $uri;
  protected $service; // Stores the service used to connect to the provider.
  protected $storage;


  /**
   * @brief Connects to the service provider and request an access token.
   */
  public function __construct() {
    $this->di = DI::getDefault();
    $this->guardian = $this->di['guardian'];
    $this->user = $this->guardian->user;

    $this->initialize();
    $this->execute();
  }


  /**
   * @brief Initializes the consumer.
   */
  private function initialize() {
    $this->uri = (new UriFactory())->createFromSuperGlobalArray($_SERVER);
    $this->uri->setQuery('');

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
  protected function askForAuthorization() {
    $uri = $this->service->getAuthorizationUri();
    header('Location: '.$uri);
    exit; // Don't proceed!
  }


  /**
   * @brief The user has granted the authorization to proceed.
   */
  protected function onAuthorizationGranted() {
    $token = $this->service->requestAccessToken($_GET['code']);
  }


  /**
   * @brief The user has denied the authorization to proceed.
   */
  protected function onAuthorizationDenied() {
    $uri = $this->uri->getRelativeUri() . '?go=go';
    header('Location: '.$uri);
    exit; // Don't proceed!
  }


  /**
   * @brief Executes the consumer authorization control flow.
   */
  protected function execute() {
    if (isset($_GET['error'])) {
      // LinkedIn returned an error
      // print $_GET['error'] . ': ' . $_GET['error_description'];
      $this->onAuthorizationDenied();
      exit;
    }
    elseif (empty($_GET['code']))
      $this->askForAuthorization();
    else
      $this->onAuthorizationGranted();
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
   * @param[in] array $userData An associative array with the user information.
   */
  protected function validate(array $userData) {
    $validation = new ValidationHelper();
    $validation->add(static::ID, new PresenceOf(["message" => "L'id è obbligatorio."]));
    $validation->add(static::EMAIL, new PresenceOf(["message" => "L'e-mail è obbligatoria."]));

    $group = $validation->validate($userData);
    if (count($group) > 0) {
      throw new Exception\InvalidFieldException("Le informazioni fornite da LinkedIn sono incomplete.");
    }
  }


  /**
   * @brief Consumes the user data.
   * @param param[in] string $userId The user identifier used by the provider.
   * @param param[in] string $userEmail The user email.
   * @param param[in] array $userData An associative array with the user information.
   */
  protected function consume($userId, $userEmail, array $userData) {
    $anonymous = $this->user->isGuest();

    $user = UserFactory::fromLogin($this->getName(), $userId);

    if ($user->isMember()) {
      if ($anonymous)
        $this->signIn($user, $userData);
      elseif ($this->user->match($user->id))
        $this->update($this->user, $userData);
      else
        throw new Exception\UserMismatchException(sprintf("Il tuo account %s è già associato ad un'altra utenza. Contatta il supporto tecnico all'indirizzo %s", $this->di['config'][$this->getName()]['name'], $this->di['config']['application']['supportEmail']));
    }
    else {
      $user = UserFactory::fromEmail($userEmail);

      if ($user->isMember()) {
        if (!$anonymous) {
          $emails = $this->user->getEmails();

          if (array_key_exists($emails, $userEmail) && $emails[$userEmail])
            $this->update($this->user, $userData);
          else
            throw new Exception\UserMismatchException(sprintf("L'e-mail del tuo account %s è già associata ad un'altra utenza attiva. <a href=\"#\">Recupera la tua password</a>.", ucfirst($this->getName())));
        }
        elseif (!$user->isVerifiedEmail($userEmail))
          $this->signIn($user, $userData);
        else
          throw new Exception\UserMismatchException(sprintf('L\'e-mail primaria del tuo account %1$s è già in uso ed è stata verificata, dunque hai già un\'utenza attiva. Per sicurezza il sistema non ti consente di autenticarti: qualcuno potrebbe infatti essersi impossessato del tuo account %1$s, al momento non associato al tuo profilo su questo sito. Se non ricordi la password, segui <a href="#">la procedura di recupero</a> e accedi utilizzando l\'e-mail e la nuova password che ti verrà spedita all\'indirizzo di posta associato al tuo account %1$s. Una volta fatto il sign in puoi collegare il tuo account %1$s.', $this->di['config'][$this->getName()]['name']));
      }
      else {
        if ($anonymous)
          $this->signUp($userData);
        else
          $this->signIn($user, $userData);
      }
    }
  }


  /**
   * @brief In case the username has already been taken, adds a sequence number to the end.
   * @param[in] string $value A potential username value.
   * @return string
   */
  protected function buildUsername($value) {
    $temp = $value;
    $counter = 1;

    while ($this->guardian->isTaken($value)) {
      $value = $temp . (string)$counter;
      $counter++;
    }

    return $value;
  }


  /**
   * @brief Updates the user object using the provided data.
   * @param[in] User $user The user instance.
   * @param param[in] array $userData An associative array with the user information.
   */
  protected function update(User $user, array $userData) {
    $user->addLogin($this->getName(), $userData[static::ID], $userData[static::PROFILE_URL], $userData[static::EMAIL], $this->isTrustworthy());
    $user->internetProtocolAddress = $_SERVER['REMOTE_ADDR'];
    //$user->save();
  }


  /**
   * @brief Returns `true` in case the linked provider is trustworthy, `false` otherwise.
   * @return bool
   */
  abstract public function isTrustworthy();


  /**
   * @brief The authenticated user joins the PitPress social network.
   */
  abstract public function join();


  /**
   * @brief Retrieves the user friends.
   * @return array
   */
  abstract public function getFriends();


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
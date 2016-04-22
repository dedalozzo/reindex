<?php

/**
 * @file OAuth2Consumer.php
 * @brief This file contains the OAuth2Consumer class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Consumer;


use OAuth\Common\Http\Uri\UriFactory;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;
use OAuth\ServiceFactory;

use Phalcon\Di;
use Phalcon\Validation\Validator\PresenceOf;

use ReIndex\Model\Member;
use ReIndex\Factory\UserFactory;
use ReIndex\Helper;
use ReIndex\Validation;
use ReIndex\Exception;


/**
 * @brief Ancestor class of each OAuth2 consumer class.
 * @nosubgrouping
 */
abstract class OAuth2Consumer implements IConsumer {

  /** @name Field Names */
  //!@{

  const ID = 'id';
  const EMAIL = 'email';
  const PROFILE_URL = 'profile_url';
  const USERNAME = 'username';

  //!@}

  protected $di; // Stores the default Dependency Injector.
  protected $config;
  protected $log; // Sotres the logger instance.
  protected $user; // Stores the current user.
  protected $uri;
  protected $service; // Stores the service used to connect to the provider.
  protected $storage;


  /**
   * @brief Connects to the service provider and request an access token.
   */
  public function __construct() {
    $this->di = Di::getDefault();
    $this->log = $this->di['log'];
    $this->config = $this->di['config'];
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

    if (is_null($this->service))
      throw new Exception\UnableToCreateServiceException('Cannot create the OAuth2 service.');
  }


  /**
   * @brief Tries to perform the user logon, with the user id given.
   * @param[in] string $userId The user identifier used by the provider.
   * @param[in] array $userData An associative array with the user information.
   * @retval bool
   */
  private function execLogonFromUserId($userId, array $userData) {
    // We search for a user associated to the $userId related to the current consumer instance. Every consumer has a
    // name to serve this purpose.
    $user = UserFactory::fromLogin($this->getName(), $userId);

    if ($user->isMember()) {
      // An user associated with the $userId has been found.

      if ($this->user->isGuest()) {
        // Since the current user is a guest, the `signIn()` is called.
        $this->signIn($user, $userData);
      }
      elseif ($this->user->match($user->id)) {
        // Since the current user is a member and the id matches with the $userId, the `update()` is called.
        $this->update($this->user, $userData);
      }
      else {
        // Unfortunately the current user is trying to add a login already associated with another user. We can't let
        // this happen.
        throw new Exception\UserMismatchException(sprintf("Il tuo account %s è già associato ad un'altra utenza. Contatta il supporto tecnico all'indirizzo %s", $this->di['config'][$this->getName()]['name'], $this->di['config']['application']['supportEmail']));
      }

      return TRUE;
    }
    else
      return FALSE;
  }


  /**
   * @brief Tries to perform the user logon, with the e-mail given.
   * @param[in] string $userEmail The user email.
   * @param[in] array $userData An associative array with the user information.
   * @retval bool
   */
  private function execLogonFromEmail($userEmail, array $userData) {
    // Since we didn't find any user associated with the $userId, we search for the $userEmail.
    $user = UserFactory::fromEmail($userEmail);

    if ($user->isMember()) {
      // An user associated with the $userEmail has been found.

      if ($this->user->isMember()) {
        // The current user is not a guest, but a member.

        if ($this->user->match($user->getId())) {
          // The user found is actually the current user.
          $this->update($this->user, $userData);
        }
        else
          throw new Exception\UserMismatchException(sprintf("L'e-mail del tuo account %s è già associata ad un'altra utenza attiva. <a href=\"#\">Recupera la tua password</a>.", $this->di['config'][$this->getName()]['name']));
      }
      elseif ($this->isTrustworthy()) {
        // The user is a guest and the provider fortunately is trustworthy. This means that $userEmail has been verified
        // by someone we trust.

        if (!$user->emails->isVerified($userEmail)) {
          // For security reason we let the user sign in only if the e-mail in our system is not verified. We must
          // prevent an attacker to execute the sign in procedure.
          $this->signIn($user, $userData); // We know $user is an instance of Member for sure.
        }
        else
          throw new Exception\UserMismatchException(sprintf('L\'e-mail primaria del tuo account %1$s è già in uso ed è stata verificata, dunque hai già un\'utenza attiva. Per sicurezza il sistema non ti consente di autenticarti: qualcuno potrebbe infatti essersi impossessato del tuo account %1$s, al momento non associato al tuo profilo su questo sito. Se non ricordi la password, segui <a href="#">la procedura di recupero</a> e accedi utilizzando l\'e-mail e la nuova password che ti verrà spedita all\'indirizzo di posta associato al tuo account %1$s. Una volta fatto il sign in puoi collegare il tuo account %1$s.', $this->di['config'][$this->getName()]['name']));
      }
      else
        throw new Exception\UserMismatchException(sprintf('L\'e-mail del tuo account %1$s è già associata ad un\'altra utenza. %1$s non è sufficientemente affidabile affinché il sistema possa autenticarti automaticamente perché consente il login anche con e-mail non verificate. <a href=\"#\">Recupera la tua password</a>.', $this->di['config'][$this->getName()]['name']));

      return TRUE;
    }
    else
      return FALSE;
  }


  /**
   * @brief Tries to perform the standard user logon.
   * @param[in] array $userData An associative array with the user information.
   * @retval Model::Member An user instance or `false`.
   */
  private function execStdLogon(array $userData) {
    if ($this->user->isGuest()) {
      // Since the current user is a guest, the sign up is called.
      return $this->signUp($userData);
    }
    else {
      // Since the current user is a member, the sign in is called, because the user is trying to add a new login.
      $this->signIn($this->user, $userData);
    }
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
    header('Location: http://'.$_SERVER['SERVER_NAME']);
    exit; // Don't proceed!
  }


  /**
   * @brief Executes the consumer authorization control flow.
   */
  protected function execute() {
    if (isset($_GET['error']))
      $this->onAuthorizationDenied();
    elseif (empty($_GET['code']))
      $this->askForAuthorization();
    else
      $this->onAuthorizationGranted();
  }


  /**
   * @brief Sends a request to the service provider.
   * @param[in] string $url The requested url.
   */
  protected function fetch($url) {
    return json_decode($this->service->request($url), TRUE);
  }


  /**
   * @brief Performs the sign in.
   * @param[in] Member $user The user instance.
   * @param[in] array $userData An associative array with the user information.
   */
  private function signIn(Member $user, array $userData) {
    $this->update($user, $userData);
    Helper\Cookie::set($user);
  }


  /**
   * @brief Performs the sign up.
   * @param[in] array $userData An associative array with the user information.
   */
  private function signUp(array $userData) {
    $user = Member::create();
    $this->update($user, $userData);
    Helper\Cookie::set($user);
    return $user;
  }


  /**
   * @brief Validates mandatory properties: id and email.
   * @details Each provider uses a different associative key to identify the user id and his own email.
   * @param[in] array $userData An associative array with the user information.
   */
  protected function validate(array $userData) {
    $validation = new Validation();
    $validation->add(static::ID, new PresenceOf(["message" => "L'id è obbligatorio."]));
    $validation->add(static::EMAIL, new PresenceOf(["message" => "L'e-mail è obbligatoria."]));

    $this->log->addDebug(sprintf('ID: %s', $userData[static::ID]));
    $this->log->addDebug(sprintf('E-mail: %s', $userData[static::EMAIL]));

    $group = $validation->validate($userData);
    if (count($group) > 0) {
      throw new Exception\InvalidFieldException(sprintf('Non hai autorizzato l\'utilizzo dell\'e-mail che è indispensabile ai fini della registrazione. Revoca l\'applicazione dal tuo profilo %s e riprova.', $this->getName()));
    }
  }


  /**
   * @brief Consumes the user data.
   * @param[in] string $userId The user identifier used by the provider.
   * @param[in] string $userEmail The user email.
   * @param[in] array $userData An associative array with the user information.
   */
  protected function consume($userId, $userEmail, array $userData) {
    if ($this->execLogonFromUserId($userId, $userData)) return;
    elseif ($this->execLogonFromEmail($userEmail, $userData)) return;
    else $this->execStdLogon($userData);
  }


  /**
   * @brief Tries to normalize the username.
   * @param[in] string $value A potential username value.
   * @retval string
   */
  protected function normalizeUsername($value) {
    // Converts the charset from uft-8 to ASCII.
    $temp = Helper\Text::convertCharset($value, FALSE, 'utf-8', 'ASCII//TRANSLIT');

    // Removes any character that is not a letter, a number, minus, hyphen or underscore.
    $temp = preg_replace('/[^.\-\w]+/', '', $temp);

    // Removes all the occurrences of minus, hyphen or underscore but first.
    $temp = Helper\Text::replaceAllButFirst('/[.\-_]/', '', $temp);

    // Finally returns the string or a substring if the username is > 24 chars.
    return (strlen($temp) > $this->config->application->usernameMaxLength) ? substr($temp, 0, $this->config->application->usernameMaxLength-1) : $temp;
  }


  /**
   * @brief In case the username has already been taken, adds a sequence number to the end.
   * @param[in] string $value A potential username value.
   * @retval string
   */
  protected function buildUsername($value) {
    $guess = $this->normalizeUsername($value);

    $temp = $guess;
    $counter = 1;

    while ($this->guardian->isTaken($guess)) {
      $guess = $temp . (string)$counter;
      $counter++;
    }

    return $guess;
  }


  /**
   * @brief Updates the user object using the provided data.
   * @param[in] Member $user The user instance.
   * @param[in] array $userData An associative array with the user information.
   */
  protected function update(Member $user, array $userData) {
    $user->logins->add($this->getName(), $userData[static::ID], @$userData[static::PROFILE_URL], $userData[static::EMAIL], @$userData[static::USERNAME], $this->isTrustworthy());
    $user->internetProtocolAddress = $_SERVER['REMOTE_ADDR'];
    $user->save();
  }


  /**
   * @copydoc IConsumer::isTrustworthy()
   */
  abstract public function isTrustworthy();


  /**
   * @copydoc IConsumer::join()
   */
  abstract public function join();


  /**
   * @copydoc IConsumer::getFriends()
   */
  abstract public function getFriends();


  /**
   * @copydoc IConsumer::getName()
   */
  abstract public function getName();


  /**
   * @copydoc IConsumer::getScope()
   */
  abstract public function getScope();

}
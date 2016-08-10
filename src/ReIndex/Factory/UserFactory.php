<?php

/**
 * @file UserFactory.php
 * @brief This file contains the UserFactory class.
 * @details
 * @author Filippo F. Fadda
 */


//! Classes which implements the factory pattern
namespace ReIndex\Factory;

use Phalcon\Di;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;
use EoC\Exception\ServerErrorException;

use ReIndex\Security\User;
use ReIndex\Helper\Cookie;


/**
 * @brief This class implements the factory pattern and it's used to creates users.
 * @nosubgrouping
 */
final class UserFactory {


  /**
   * @brief This function tries to recognize a user from his id and the secret token. In case the user has been
   * recognized, an Member object is returned, else this function returns an Anonymous instance.
   * @retval User::IUser An instance of the user has been recognized by his cookie.
   * @todo Raise an exception when the user is banned, because obviously he can't login.
   */
  public static function fromCookie() {
    // A console script runs as System user.
    if (php_sapi_name() == 'cli') return new User\System();

    $di = Di::getDefault();
    $couch = $di['couchdb'];
    $security = $di['security'];

    if (isset($_COOKIE['id']) && ($_COOKIE['id'] != 'deleted') && isset($_COOKIE['token']) && ($_COOKIE['token'] != 'deleted')) {
      $id = $_COOKIE['id'];
      $token = $_COOKIE['token'];

      try {
        // Gets the user.
        $user = $couch->getDoc(Couch::STD_DOC_PATH, $id);
      }
      catch(ServerErrorException $e) { // The user doesn't exist anymore.
        Cookie::delete();
        return new User\Anonymous();
      }

      if ($security->checkHash($user->id.$_SERVER['REMOTE_ADDR'], $token))
        return $user;
      else {
        Cookie::delete();
        return new User\Anonymous();
      }
    }
    else
      return new User\Anonymous();
  }


  /**
   * @brief Searches for the user identified by the identifier associated with the specific provider. If any returns it,
   * otherwise return an Anonymous instance.
   * @param[in] string $consumerName The consumer name.
   * @param[in] string $userId The user identifier used by the provider.
   * @retval Security::Member::IUser An user instance.
   */
  public static function fromLogin($consumerName, $userId) {
    $di = Di::getDefault();
    $couch = $di['couchdb'];

    $opts = new ViewQueryOpts();
    $loginName = $userId.'@'.$consumerName;
    $opts->setKey($loginName)->setLimit(1);
    // members/byConsumer/view
    $result = $couch->queryView('members', 'byConsumer', 'view', NULL, $opts);

    if (!$result->isEmpty())
      return $couch->getDoc(Couch::STD_DOC_PATH, $result[0]['id']);
    else
      return new User\Anonymous();
  }


  /**
   * @brief Searches for the user identified by e-mail, if any returns it, otherwise return an Anonymous instance.
   * @param[in] string $email The user email.
   * @retval User::IUser An user instance.
   */
  public static function fromEmail($email) {
    $di = Di::getDefault();
    $couch = $di['couchdb'];

    $opts = new ViewQueryOpts();
    $opts->setKey($email)->setLimit(1);
    //members/byEmail/view
    $result = $couch->queryView('members', 'byEmail', 'view', NULL, $opts);

    if (!$result->isEmpty())
      return $couch->getDoc(Couch::STD_DOC_PATH, $result[0]['id']);
    else
      return new User\Anonymous();
  }


  /**
   * @brief Searches for the user identified with the provided username, if any returns it, otherwise return an Anonymous
   * instance.
   * @param[in] string $username The username.
   * @retval User::IUser An user instance.
   */
  public static function fromUsername($username) {
    $di = Di::getDefault();
    $couch = $di['couchdb'];

    $opts = new ViewQueryOpts();
    $opts->setKey($username)->setLimit(1);
    // members/byUsername/view
    $result = $couch->queryView('members', 'byUsername', 'view', NULL, $opts);

    if (!$result->isEmpty())
      return $couch->getDoc(Couch::STD_DOC_PATH, $result[0]['value']);
    else
      return new User\Anonymous();
  }

}
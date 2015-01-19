<?php

/**
 * @file UserFactory.php
 * @brief This file contains the UserFactory class.
 * @details
 * @author Filippo F. Fadda
 */


//! This is the namespace of all classes that implement the factory pattern.
namespace PitPress\Factory;

use Phalcon\DI;

use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;
use ElephantOnCouch\Exception\ServerErrorException;

use PitPress\Security\User;
use PitPress\Security\Consumer\IConsumer;
use PitPress\Helper\Cookie;


/**
 * @brief This class implements the factory pattern and it's used to creates users.
 * @nosubgrouping
 */
class UserFactory {


  /**
   * @brief This function tries to recognize a user from his id and the secret token. In case the user has been
   * recognized, an User object is returned, else this function returns an AnonymousUser instance.
   * @return User\IUser An instance of the user has been recognized by his cookie.
   */
  public static function fromCookie() {
    // A console script runs as System user.
    if (php_sapi_name() == 'cli') return new User\System();

    $di = DI::getDefault();
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
        return new User\AnonymousUser();
      }

      if ($security->checkHash($user->id.$_SERVER['REMOTE_ADDR'], $token))
        return $user;
      else {
        Cookie::delete();
        return new User\AnonymousUser();
      }
    }
    else
      return new User\AnonymousUser();
  }


  /**
   * @brief Searches for the user identified by the specified id, related to a specific consumer. If any returns it,
   * otherwise return an AnonymousUser instance.
   * @param[in] IConsumer $consumer A consumer instance.
   * @return User\IUser An user instance.
   */
  public static function fromConsumer(IConsumer $consumer) {
    $di = DI::getDefault();
    $couch = $di['couchdb'];

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1);
    $result = $couch->queryView("users", "byProvider", [$consumer->getName(), $consumer->getId()], $opts);

    if (!$result->isEmpty())
      return $couch->getDoc(Couch::STD_DOC_PATH, $result[0]['id']);
    else
      return new User\AnonymousUser();
  }


  /**
   * @brief Searches for the user identified by the specified emails, if any returns it, otherwise return an AnonymousUser
   * instance.
   * @param[in] array $emails
   * @return User\IUser An user instance.
   */
  public static function fromEmails(array $emails) {
    $di = DI::getDefault();
    $couch = $di['couchdb'];

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1);
    $result = $couch->queryView("users", "byEmail", $emails, $opts);

    if (!$result->isEmpty())
      return $couch->getDoc(Couch::STD_DOC_PATH, $result[0]['id']);
    else
      return new User\AnonymousUser();
  }

}
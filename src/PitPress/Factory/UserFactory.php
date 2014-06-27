<?php

/**
 * @file UserFactory.php
 * @brief This file contains the UserFactory class.
 * @details
 * @author Filippo F. Fadda
 */


//! This is the namespace of all classes that implement the factory pattern.
namespace PitPress\Factory;


use PitPress\Model\User;

use Phalcon\DI;

use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;


/**
 * @brief This class implements the factory pattern and it's used to creates users.
 * @nosubgrouping
 */
class UserFactory {

  /**
   * @brief This function tries to recognize a user from his ID and the secret token. In case the user has been
   * recognized, an user object is returned, else this function returns `null`.
   * @return User An instance of the user has been recognized by his cookie.
   */
  public static function getFromCookie() {
    $di = DI::getDefault();
    $couch = $di['couchdb'];
    $security = $di['security'];

    if (isset($_COOKIE['id']) && ($_COOKIE['id'] != 'deleted') && isset($_COOKIE['token']) && ($_COOKIE['token'] != 'deleted')) {
      $id = $_COOKIE['id'];
      $token = $_COOKIE['token'];

      // Gets the user.
      $user = $couch->getDoc(Couch::STD_DOC_PATH, $id);

      if ($security->checkHash($user->id.$_SERVER['REMOTE_ADDR'], $token))
        return $user;
      else {
        // To avoid Internet Explorer 6.x implementation issues.
        header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
        header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');

        // Deletes the cookies.
        setcookie("id", "", time(), "/", $di['config']['application']->serverName);
        setcookie("token", "", time(), "/", $di['config']['application']->serverName);

        return NULL;
      }
    }
    else
      return NULL;
  }


  /**
   * @brief Returns `true` in case exist a user registered with specified username, `false` otherwise.
   * @param[in] string $username The username.
   * @return bool
   */
  public static function isUsernameTaken($username) {
    $di = DI::getDefault();
    $couch = $di['couchdb'];

    $opts = new ViewQueryOpts();
    $opts->setLimit(1)->setKey($username);

    $result = $couch->queryView("users", "byUsername", NULL, $opts);

    if ($result->isEmpty())
      return FALSE;
    else
      return TRUE;
  }

}
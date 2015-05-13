<?php

/**
 * @file Cookie.php
 * @brief This file contains the Cookie class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Helper;


use Phalcon\DI;

use PitPress\Model\User;


/**
 * @brief This helper class contains routines to handle cookies.
 * @nosubgrouping
 */
class Cookie {


  /**
   * @brief Creates a new cookie using the provided id and token.
   * @param[in] User $user An User.
   */
  public static function set(User $user) {
    $di = DI::getDefault();
    $security = $di['security'];

    // Creates a token based on the user id and his IP address, obviously encrypted.
    $token = $security->hash($user->id.$user->internetProtocolAddress);

    // To avoid Internet Explorer 6.x implementation issues. I don't fuckin care about IE 6 but this code worked for
    // years, so let's use it.
    header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
    header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');

    // Finally let's write the id and the token.
    setcookie("id", $user->id, mktime(0, 0, 0, 12, 12, 2030), "/", $di['config']['application']->domainName);
    setcookie("token", $token, mktime(0, 0, 0, 12, 12, 2030), "/", $di['config']['application']->domainName);
  }


  /**
   * @brief Deletes the user cookies.
   */
  public static function delete() {
    $di = DI::getDefault();

    $di['log']->addNotice("log delete");

    // To avoid Internet Explorer 6.x implementation issues.
    header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
    header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');

    // Deletes the cookies.
    setcookie("id", "", 0, "/", $di['config']['application']->domainName);
    setcookie("token", "", 0, "/", $di['config']['application']->domainName);
  }

}
<?php

//! @file Cookie.php
//! @brief This file contains the Cookie class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Helper;


use Phalcon\DI;


/**
 * @brief This helper class contains routines to handle cookies.
 * @nosubgrouping
 */
class Cookie {


  /**
   * @brief Creates a new cookie using the provided id and token.
   * @param[in] string $id The user id.
   * @param[in] string token A generated token.
   */
  public static function set($id, $token) {
    $di = DI::getDefault();

    // To avoid Internet Explorer 6.x implementation issues. I don't fuckin care about IE 6 but this code worked for
    // years, so let's use it.
    header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
    header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');

    // Finally let's write the id and the token.
    setcookie("id", $id, mktime(0, 0, 0, 12, 12, 2030), "/", $di['config']['application']->domainName);
    setcookie("token", $token, mktime(0, 0, 0, 12, 12, 2030), "/", $di['config']['application']->domainName);
  }


  /**
   * @brief Deletes the user cookies.
   */
  public static function delete() {
    $di = DI::getDefault();

    $di['monolog']->addNotice("log delete");

    // To avoid Internet Explorer 6.x implementation issues.
    header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
    header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');

    // Deletes the cookies.
    setcookie("id", "", time(), "/", $di['config']['application']->domainName);
    setcookie("token", "", time(), "/", $di['config']['application']->domainName);
    setcookie("test", "", 0, "/", $di['config']['application']->domainName);
  }

}
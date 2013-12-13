<?php

//! @file UserFactory.php
//! @brief This file contains the UserFactory class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Factory;


use PitPress\Model\User;


class UserFactory {

  public static function getFromCookie() {
    if (isset($_COOKIE['id']) && ($_COOKIE['id'] != 'deleted') && isset($_COOKIE['md5']) && ($_COOKIE['md5'] != 'deleted')) {
      $idMember = $_COOKIE['id'];
      $md5 = $_COOKIE['md5'];

      $sql = "SELECT idMember, name, surname, nickName, email, password, ipAddress, admin, homePage, regDate FROM Member WHERE idMember = ".mysql_real_escape_string($idMember)." AND confirmed = 1";

      $result = mysql_query($sql, $connection);

      if (mysql_num_rows($result)) {
        $row = mysql_fetch_object($result);
        $temp = md5(crypt($row->idMember.$row->ipAddress.$row->regDate, 'jzojhghgfd'));

        if ($md5 == $temp)
          return $row;
        else
          return null;
      }
      else
        return null;

      mysql_free_result($result);

    }
    else
      return null;
  }


} 
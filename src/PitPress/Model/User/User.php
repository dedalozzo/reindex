<?php

//! @file User.php
//! @brief This file contains the User class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model\User;


use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Model\Storable;
use PitPress\Extension;


//! @brief This class is used to represent a registered user.
//! @nosubgrouping
class User extends Storable implements Extension\ICount {
  use Extension\TCount;


  //! @brief Given a e-mail, returns the gravatar URL for the corresponding user.
  //! @param[in] string $email The user e-mail.
  //! @return string
  public static function getGravatar($email) {
    return 'http://gravatar.com/avatar/'.md5(strtolower($email)).'?d=identicon';
  }


  //! @brief Last time the user has logged in.
  public function getLastVisit($value) {
    $this->meta['lastVisit'] = $value;
  }


  //! @brief Returns the user's reputation.
  //! @return integer
  public function getReputation() {
    $opts = new ViewQueryOpts();
    $opts->setKey([$this->id]);

    $result = $this->couch->queryView("reputation", "perUser", NULL, $opts);

    if (!empty($result['rows'])) {
      $reputation =  $result['rows'][0]['value'];

      if ($reputation > 1)
        return $reputation;
      else
        return 1;
    }
    else
      return 1;
  }


  //! @name Authentication Methods
  // @{

  public static function login($email, $password) {
    $error = null;

    $login = strtolower($email);
    $pass = md5($password);

    $sql = "SELECT idMember, ipAddress, regDate, confirmed FROM Member WHERE nickName = '$login' AND password = '$pass'";

    /*if ($result = mysql_query($sql, $connection))
      if (mysql_num_rows($result)) {
        $row = mysql_fetch_object($result);
        if ($row->confirmed) {
          $sql = "UPDATE Member SET ipAddress = '".$_SERVER['REMOTE_ADDR']."' WHERE idMember = ".$row->idMember;
          if (mysql_query($sql, $connection)) {
            $str = $row->idMember.$_SERVER['REMOTE_ADDR'].$row->regDate;
            $md5 = md5(crypt($str, 'jzojhghgfd'));

            // to avoid Internet Explorer 6.x implementation issues
            header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
            header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');

            setcookie("id", $row->idMember, mktime(0, 0, 0, 12, 12, 2030), "/", constant('DOMAIN'));
            setcookie("md5", $md5, mktime(0, 0, 0, 12, 12, 2030), "/", constant('DOMAIN'));
          }
          else
            $error = "C'� stato un errore nella procedura di login, riprova tra un minuto, grazie.";
        }
        else
          $error = "L'utente risulta iscritto, ma l'iscrizione non � ancora stata confermata. Segui le istruzioni ricevute nella e-mail di attivazione che ti � stata inviata. Se ancora non l'hai ricevuta, richiedi una nuova <a href=\"index.php?entity=eactivationemail\" target=\"_self\">e-mail di attivazione</a>.";
      }
      else
        $error = "Non vi � nessun utente registrato con la login inserita o la password � errata.";
    else $error = mysql_error();
    */

    if (isset($error)) { // reset cookies
      // to avoid Internet Explorer 6.x implementation issues
      header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
      header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');

      setcookie("id", "", time(), "/", constant('DOMAIN'));
      setcookie("md5", "", time(), "/", constant('DOMAIN'));
    }

    //mysql_free_result($result);
    return $error;
  }


  public function logout() {
    // to avoid Internet Explorer 6.x implementation issues
    header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
    header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');

    // set a null cookie and redirect to the home page
    setcookie("id", "", 0, "/", constant('DOMAIN'));
    setcookie("md5", "", 0, "/", constant('DOMAIN'));
    setcookie("test", "", 0, "/", constant('DOMAIN'));

    header("Location: index.php");
    exit;
  }


  public function activate($confirmationHash) {
    //$sql = "SELECT idMember, UNIX_TIMESTAMP(lastUpdate), confirmed, email FROM Member WHERE confirmHash = '".mysql_real_escape_string($confirmHash)."'";
    /*$result = mysql_query($sql, $connection) or die(mysql_error());

    if ($row = mysql_fetch_row($result)) {
      if ($row[2])
        go_to("index.php?entity=elogin"); // member already activated
      elseif (((strtotime("now") - $row[1]) / 3600) > 24) // the hash is expired
        go_to("index.php?entity=eitem&idItem=29108"); // hash code is expired
      else {
        $sql = "UPDATE Member SET confirmed = 1, lastUpdate = NOW() WHERE idMember = ".$row[0];
        mysql_query($sql, $connection) or die(mysql_error());

        $error = subscribeNewsletter($row[3]);
        if (isset($error))
          go_to("index.php?entity=eitem&idItem=29109"); // unable to send newsletter subscription email
        else
          go_to("index.php?entity=elogin"); // member activated
      }
    }
    else
      go_to("index.php?entity=eitem&idItem=29110"); // invalid hash code
    */
  }


  public function facebookConnect() {

  }


  public function googleConnect() {

  }


  public function linkedinConnect() {

  }


  public function githubConnect() {

  }



  //! @brief Authenticate the user.
  public function authenticate() {
    $this->meta['authenticated'] = "true";
  }


  //! @brief Returns TRUE if the user has been authenticated.
  public function isAuthenticated() {
    return isset($this->meta['authenticated']);
  }


  public function getConfirmationHash($value) {
    $this->meta['confirmationHash'] = $value;
  }

  //@}


  //! @name Ban Management Methods
  // @{

  //! @brief Bans the user.
  public function ban($days) {
    $this->meta['bannedOn'] = time();
    $this->meta['bannedFor'] = $days;
    $this->meta['banned'] = "true";
  }


  //! @brief Removes the ban.
  public function unban() {
    if ($this->isMetadataPresent('banned'))
      unset($this->meta['banned']);
  }


  //! @brief Returns `true` if the user has been banned.
  public function isBanned() {
    return isset($this->meta['banned']);
  }

  //@}


  //! @cond HIDDEN_SYMBOLS

  public function getAge() {

  }


  public function getFirstName() {
    return $this->meta['firstName'];
  }


  public function issetFirstName() {
    return isset($this->meta['firstName']);
  }


  public function setFirstName($value) {
    $this->meta['firstName'] = $value;
  }


  public function getLastName() {
    return $this->meta['lastName'];
  }


  public function issetLastName() {
    return isset($this->meta['lastName']);
  }


  public function setLastName($value) {
    $this->meta['lastName'] = $value;
  }


  public function getDisplayName() {
    return $this->meta['displayName'];
  }


  public function issetDisplayName() {
    return isset($this->meta['displayName']);
  }


  public function setDisplayName($value) {
    $this->meta['displayName'] = $value;
  }


  public function getEmail() {
    return $this->meta['email'];
  }


  public function issetEmail() {
    return isset($this->meta['email']);
  }


  public function setEmail($value) {
    $this->meta['email'] = strtolower($value);
  }


  public function getPassword() {
    return $this->meta['password'];
  }


  public function issetPassword() {
    return isset($this->meta['password']);
  }


  public function setPassword($value) {
    $this->meta['password'] = $value;
  }


  public function getSex() {
    return $this->meta['sex'];
  }


  public function issetSex() {
    return isset($this->meta['sex']);
  }


  public function setSex($value) {
    $this->meta['sex'] = $value;
  }


  public function getBirthday() {
    return $this->meta['birthday'];
  }


  public function issetBirthday() {
    return isset($this->meta['birthday']);
  }


  public function setBirthday($value) {
    $this->meta['birthday'] = $value;
  }


  public function getAbout() {
    return $this->meta['about'];
  }


  public function issetAbout() {
    return isset($this->meta['about']);
  }


  public function setAbout($value) {
    $this->meta['about'] = $value;
  }
  

  public function getIPAddress($value) {
    $this->meta['ipAddress'] = $value;
  }


  public function issetIPAddress() {
    return isset($this->meta['ipAddress']);
  }


  public function setIPAddress($value) {
    $this->meta['ipAddress'] = $value;
  }


  public function getCreationDate() {
    return $this->meta['creationDate'];
  }


  public function issetCreationDate() {
    return isset($this->meta['creationDate']);
  }


  public function setCreationDate($value) {
    $this->meta['creationDate'] = $value;
  }


  public function unsetCreationDate() {
    if ($this->isMetadataPresent('creationDate'))
      unset($this->meta['creationDate']);
  }


  //! @endcond

}
<?php

//! @file AuthController.php
//! @brief This file contains the AuthController class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use OAuth\Common\Http\Uri\UriFactory;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials; 
use OAuth\ServiceFactory;

use Phalcon\Mvc\View;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;

use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Exception\InvalidTokenException;
use PitPress\Exception\UserNotConfirmedException;
use PitPress\Exception\UserNotFoundException;
use PitPress\Exception\WrongPasswordException;


//! @brief Controller of Authentication actions.
//! @nosubgrouping
class AuthController extends BaseController {

  //! @brief Sign in with a PitPress account.
  public function signInAction() {
    if ($this->request->isPost()) {

      try {

        if (!$this->security->checkToken())
          throw new InvalidTokenException("Il token è invalido, la richiesta non può essere evasa.");

        $validation = new Validation();

        $validation->setFilters("email", "trim");
        $validation->setFilters("email", "lower");

        $validation->setFilters("password", "trim");

        $validation->add("email", new PresenceOf(["message" => "L'e-mail è richiesta"]));
        $validation->add("email", new Email(["message" => "L'e-mail non è valida"]));

        $validation->add("password", new PresenceOf(["message" => "La password è richiesta"]));

        $email = $this->request->getPost('email');
        //$password = $this->security->hash($this->request->getPost('password'));
        $password = md5($this->request->getPost('password'));

        $opts = new ViewQueryOpts();
        $opts->setKey($email)->setLimit(1);

        $rows = $this->couch->queryView("users", "byEmail", NULL, $opts)['rows'];

        if (empty($rows))
          throw new UserNotFoundException("Non vi è nessun utente registrato con l'e-mail inserita o la password è errata.");

        // Gets the user.
        $user = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $rows[0]['id']);

        // Checks if the user has confirmed his registration.
        if (!$user->isConfirmed())
          throw new UserNotConfirmedException("L'utente risulta iscritto, ma l'iscrizione non è ancora stata confermata. Segui le istruzioni ricevute nella e-mail di attivazione che ti è stata inviata. Se ancora non l'hai ricevuta, <a href=\"".$this->baseUri."/invia-email-attivazione/\">richiedi una nuova e-mail di attivazione</a>.");

        if (($user->password != $password))
          throw new WrongPasswordException("Non vi è nessun utente registrato con la login inserita o la password è errata. <a href=\"".$this->baseUri."/resetta-password/\">Hai dimenticato la password?</a>");

        // Updates the ip address with the current one.
        $user->ipAddress = $_SERVER['REMOTE_ADDR'];

        // Creates a token based on the user id and his IP address, obviously encrypted.
        $token = $this->security->hash($user->id.$user->ipAddress);

        // To avoid Internet Explorer 6.x implementation issues. I don't fuckin care about IE 6 but this code worked so
        // let's use it.
        header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
        header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');

        // Finally I write the id and the token.
        setcookie("id", $user->id, mktime(0, 0, 0, 12, 12, 2030), "/", $this->application->serverName);
        setcookie("token", $token, mktime(0, 0, 0, 12, 12, 2030), "/", $this->application->serverName);

        $user->save();

        return $this->response->redirect("index");
      }
      catch (\Exception $e) {
        // To avoid Internet Explorer 6.x implementation issues.
        header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
        header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
        setcookie("id", "", time(), "/", $this->application->serverName);
        setcookie("token", "", time(), "/", $this->application->serverName);

        // Displays the error message.
        $this->flashSession->error($e->getMessage());
      }

    }

    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  //! @brief Sign out.
  public function signOutAction() {

    // To avoid Internet Explorer 6.x implementation issues.
    header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
    header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');

    // Sets a null cookie and redirect to the home page.
    setcookie("id", "", 0, "/", $this->application->serverName);
    setcookie("token", "", 0, "/", $this->application->serverName);
    setcookie("test", "", 0, "/", $this->application->serverName);

    header("Location: /");
    exit;
  }


  //! @brief Sign up a PitPress account.
  public function signUpAction() {
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  //! @brief Reset the password of your PitPress account.
  public function resetPasswordAction() {
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  //! @brief Sends an e-mail with a confirmation link to authenticate the user's e-mail address.
  public function sendActivationEmailAction() {
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  //! @brief The user has clicked on the confirmation link sent via e-mail by the previous action.
  public function activateAction($confirmationHash) {
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


  //! @brief Sign in with Facebook.
  public function facebookAction() {
    $uriFactory = new UriFactory();
    $currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
    $currentUri->setQuery('');

    $storage = new Session();

    $credentials = new Credentials($this->di['config']['facebook']['key'], $this->di['config']['facebook']['secret'], $currentUri->getAbsoluteUri());
    $serviceFactory = new ServiceFactory();
    $service = $serviceFactory->createService('facebook', $credentials, $storage, []);

    if (!empty($_GET['code'])) {
      // This was a callback request from facebook, get the token
      $token = $service->requestAccessToken($_GET['code']);

      // Send a request with it
      $result = json_decode($service->request('/me'), true);

      // Show some of the resultant data
      echo 'Your unique facebook user id is: ' . $result['id'] . ' and your name is ' . $result['name'];

    }
    else {
      $url = $service->getAuthorizationUri();
      //$this->response->redirect($url);
      header('Location: ' . $url);
    }

  }


  //! @brief Sign in with Google+.
  public function googleAction() {
    $uriFactory = new UriFactory();
    $currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
    $currentUri->setQuery('');

    $storage = new Session();

    $credentials = new Credentials($this->di['config']['google']['key'], $this->di['config']['google']['secret'], $currentUri->getAbsoluteUri());
    $serviceFactory = new ServiceFactory();
    $service = $serviceFactory->createService('google', $credentials, $storage, ['userinfo_email', 'userinfo_profile']);

    if (!empty($_GET['code'])) {
      // This was a callback request from google, get the token
      $service->requestAccessToken($_GET['code']);

      // Send a request with it
      $result = json_decode($service->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);

      // Show some of the resultant data
      echo 'Your unique google user id is: ' . $result['id'] . ' and your name is ' . $result['name'];

    }
    else {
      $url = $service->getAuthorizationUri();
      header('Location: ' . $url);
    }

  }


  //! @brief Sign in with LinkedIn.
  public function linkedinAction() {
    $uriFactory = new UriFactory();
    $currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
    $currentUri->setQuery('');

    $storage = new Session();

    $credentials = new Credentials($this->di['config']['linkedin']['key'], $this->di['config']['linkedin']['secret'], $currentUri->getAbsoluteUri());
    $serviceFactory = new ServiceFactory();
    $service = $serviceFactory->createService('linkedin', $credentials, $storage, ['r_basicprofile']);

    if (!empty($_GET['code'])) {
      // This was a callback request from linkedin, get the token
      $token = $service->requestAccessToken($_GET['code']);

      // Send a request with it. Please note that XML is the default format.
      $result = json_decode($service->request('/people/~?format=json'), true);

      // Show some of the resultant data
      echo 'Your linkedin first name is ' . $result['firstName'] . ' and your last name is ' . $result['lastName'];

    }
    else {
      // state is used to prevent CSRF, it's required
      $url = $service->getAuthorizationUri(['state' => 'DCEEFWF45453sdffef424']);
      header('Location: ' . $url);
    }

  }


  //! @brief Sign in with GitHub.
  public function githubAction() {
    $uriFactory = new UriFactory();
    $currentUri = $uriFactory->createFromSuperGlobalArray($_SERVER);
    $currentUri->setQuery('');

    $storage = new Session();

    $credentials = new Credentials($this->di['config']['github']['key'], $this->di['config']['github']['secret'], $currentUri->getAbsoluteUri());
    $serviceFactory = new ServiceFactory();
    $service = $serviceFactory->createService('GitHub', $credentials, $storage, ['user']);

    if (!empty($_GET['code'])) {
      // This was a callback request from github, get the token
      $service->requestAccessToken($_GET['code']);

      $result = json_decode($service->request('user/emails'), true);

      echo 'The first email on your github account is ' . $result[0];

    }
    else {
      $url = $service->getAuthorizationUri();
      header('Location: ' . $url);
    }

  }

}
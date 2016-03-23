<?php

/**
 * @file AuthController.php
 * @brief This file contains the AuthController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Controller;


use Phalcon\Mvc\View;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Confirmation;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use ReIndex\Exception;
use ReIndex\Validation;
use ReIndex\Helper\Cookie;
use ReIndex\Model\Member;
use ReIndex\Security\Consumer;
use ReIndex\Validator\Password;
use ReIndex\Validator\Username;


/**
 * @brief Controller of Authentication actions.
 * @nosubgrouping
 * @todo Add the support for a stronger password encryption.
 */
class AuthController extends BaseController {


  /**
   * @brief Performs the consumer join operation.
   * @param[in] Security::OAuth2Consumer $consumer A consumer instance.
   */
  protected function join(Consumer\IConsumer $consumer) {
    if ($this->user->isMember())
      $this->addSocialLogin($consumer);
    else
      $this->performLogon($consumer);
  }


  /**
   * @brief Try to add the social login to the current member.
   * @param[in] Security::OAuth2Consumer $consumer A consumer instance.
   */
  protected function addSocialLogin(Consumer\IConsumer $consumer) {
    $this->flash->clear();

    try {
      $consumer->join();
      $this->flash->success(sprintf('Congratulations, your %s social login has been added.', $this->di['config'][$consumer->getName()]['name']));
    }
    catch (\Exception $e) {
      // Displays the error message.
      $this->flash->error($e->getMessage());
    }

    header('Location: /' . $this->user->username . '/settings/logins/');
  }


  /**
   * @brief Performs the logon using the specified consumer.
   * @param[in] Security::OAuth2Consumer $consumer A consumer instance.
   */
  protected function performLogon(Consumer\IConsumer $consumer) {
    $this->flash->clear();

    try {
      $consumer->join();
      return $this->redirectToReferrer();
    }
    catch (\Exception $e) {
      // Displays the error message.
      $this->flash->error($e->getMessage());
    }

    $this->view->setVar("logon", TRUE);
    $this->view->setVar('title', 'Unisciti al più grande social network italiano di sviluppatori');

    $this->assets->addJs($this->dist."/js/tab.min.js", FALSE);

    $this->view->pick('views/auth/logon');
  }


  /**
   * @brief Performs the Sign In.
   */
  protected function signIn() {
    $this->view->setVar("signin", TRUE);

    // The validation object must be created in any case.
    $validation = new Validation();
    $this->view->setVar('validation', $validation);

    if ($this->request->isPost()) {

      try {

        if (!$this->security->checkToken())
          throw new Exception\InvalidTokenException("Il token è invalido, la richiesta non può essere evasa. Riprova.");

        $validation->setFilters("email", "trim");
        $validation->setFilters("email", "lower");
        $validation->add("email", new PresenceOf(["message" => "L'e-mail è obbligatoria."]));
        $validation->add("email", new Email(["message" => "L'e-mail non è valida."]));

        $validation->add("password", new PresenceOf(["message" => "La password è obbligatoria."]));

        $group = $validation->validate($_POST);
        if (count($group) > 0) {
          throw new Exception\InvalidFieldException("I campi sono incompleti o i valori indicati non sono validi. Gli errori sono segnalati in rosso sotto ai rispettivi campi d'inserimento.");
        }

        // Filters only the messages generated for the field 'name'.
        /*foreach ($validation->getMessages()->filter('email') as $message) {
          $this->flash->notice($message->getMessage());
          break;
        }*/

        $email = $this->request->getPost('email');
        //$password = $this->security->hash($this->request->getPost('password'));
        $password = md5($this->request->getPost('password'));

        $opts = new ViewQueryOpts();
        $opts->setKey($email)->setLimit(1);

        $rows = $this->couch->queryView("members", "byEmail", NULL, $opts);

        if ($rows->isEmpty())
          throw new Exception\UserNotFoundException("Non vi è nessun utente registrato con l'e-mail inserita o la password è errata.");

        // Gets the user.
        $user = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $rows[0]['id']);

        // Checks if the user has verified his e-mail.
        if (!$user->isVerifiedEmail($email))
          throw new Exception\EmailNotVerifiedException("L'utente risulta iscritto, ma l'iscrizione non è ancora stata confermata. Segui le istruzioni ricevute nella e-mail di attivazione che ti è stata inviata. Se ancora non l'hai ricevuta, <a href=\"//".$this->domainName."/invia-email-attivazione/\">richiedi una nuova e-mail di attivazione</a>.");

        if ($user->password != $password)
          throw new Exception\WrongPasswordException("Non vi è nessun utente registrato con la login inserita o la password è errata. <a href=\"//".$this->domainName."/resetta-password/\">Hai dimenticato la password?</a>");

        // Updates the ip address with the current one.
        $user->internetProtocolAddress = $_SERVER['REMOTE_ADDR'];

        Cookie::set($user);

        $user->save();

        return $this->redirectToReferrer($user);
      }
      catch (\Exception $e) {
        Cookie::delete();

        // Displays the error message.
        $this->flash->error($e->getMessage());
      }
    }
    else
      $this->setReferrer();
  }


  /**
   * @brief Performs the Sign Up.
   */
  protected function signUp() {
    $this->view->setVar("signup", TRUE);

    // The validation object must be created in any case.
    $validation = new Validation();
    $this->view->setVar('validation', $validation);

    if ($this->request->isPost()) {

      try {
        $validation->setFilters("username", "trim");
        $validation->add("username", new Username());

        $validation->setFilters("email", "trim");
        $validation->setFilters("email", "lower");
        $validation->add("email", new PresenceOf(["message" => "L'e-mail è obbligatoria."]));
        $validation->add("email", new Email(["message" => "L'e-mail non è valida."]));

        $validation->add("password", new Password());
        $validation->add('password', new Confirmation(
          [
            'message' => "La password è diversa da quella di conferma.",
            'with' => 'confirmPassword'
          ]));

        $group = $validation->validate($_POST);
        if (count($group) > 0) {
          throw new Exception\InvalidFieldException("I campi sono incompleti o i valori indicati non sono validi. Gli errori sono segnalati in rosso sotto ai rispettivi campi d'inserimento.");
        }

        // Filters only the messages generated for the field 'name'.
        /*foreach ($validation->getMessages()->filter('email') as $message) {
          $this->flash->notice($message->getMessage());
          break;
        }*/

        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        //$password = $this->security->hash($this->request->getPost('password'));
        $password = md5($this->request->getPost('password'));

        $opts = new ViewQueryOpts();
        $opts->setKey($email)->setLimit(1);

        $rows = $this->couch->queryView("members", "byEmail", NULL, $opts);

        if (!$rows->isEmpty())
          throw new Exception\InvalidEmailException("Sei già registrato. <a href=\"#signin\">Fai il login!</a>");

        $user = new Member(); // We don't use Member::create() since the user must confirm his e-mail address to sign in.
        $user->username = $username;
        $user->addEmail($email);
        $user->password = $password;

        // Updates the ip address with the current one.
        $user->internetProtocolAddress = $_SERVER['REMOTE_ADDR'];

        $user->save();

        return $this->redirectToReferrer($user);
      }
      catch (\Exception $e) {
        // Displays the error message.
        $this->flash->error($e->getMessage());
      }
    }
    else
      $this->setReferrer();
  }


  /**
   * @brief Displays the logon form.
   */
  public function logonAction() {
    if ($this->user->isMember()) return header('Location: /');

    if ($this->flash->has('error'))
      $this->flash->clear();

    if ($this->request->getPost('signup'))
      $this->signUp();
    else
      $this->signIn();

    $this->view->setVar('title', 'Unisciti al più grande social network italiano di sviluppatori');

    $this->assets->addJs($this->dist."/js/tab.min.js", FALSE);

    $this->view->pick('views/auth/logon');
  }


  /**
   * @brief Sign out.
   */
  public function signOutAction() {
    if ($this->user->isGuest())
      return $this->redirect();

    Cookie::delete();

    // Displays the error message.
    $this->flash->success("Disconnessione avvenuta con successo.");

    $this->view->disable();

    return $this->redirect();
  }


  /**
   * @brief Reset the password of your ReIndex account.
   */
  public function resetPasswordAction() {
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  /**
   * @brief Sends an e-mail with a confirmation link to authenticate the user's e-mail address.
   */
  public function sendActivationEmailAction() {
    $this->view->disableLevel(View::LEVEL_LAYOUT);
  }


  /**
   * @brief The user has clicked on the confirmation link sent via e-mail by the previous action.
   */
  public function activateAction($hash) {
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


  /**
   * @brief Sign in with Facebook.
   */
  public function facebookAction() {
    $this->join(new Consumer\FacebookConsumer);
  }


  /**
   * @brief Sign in with LinkedIn.
   */
  public function linkedinAction() {
    $this->join(new Consumer\LinkedInConsumer());
  }


  /**
   * @brief Sign in with GitHub.
   */
  public function githubAction() {
    $this->join(new Consumer\GitHubConsumer());
  }


  /**
   * @brief Sign in with Google+.
   */
  public function googleAction() {
    $this->join(new Consumer\GoogleConsumer());
  }

}
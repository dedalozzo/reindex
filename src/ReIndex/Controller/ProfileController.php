<?php

/**
 * @file ProfileController.php
 * @brief This file contains the ProfileController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Controller;

use ReIndex\Factory\UserFactory;
use ReIndex\Helper;
use ReIndex\Exception;
use ReIndex\Validation;
use ReIndex\Validator\Password;
use ReIndex\Validator\Username;
use ReIndex\Security\User\IUser;
use ReIndex\Security\Role\ModeratorRole;

use Phalcon\Mvc\View;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;


/**
 * @brief Member's profile controller.
 * @nosubgrouping
 */
class ProfileController extends ListController {


  /**
   * @brief Given a username returns the correspondent user.
   * @param[in] string $username A username.
   * @retval User::IUser
   */
  protected function getUser($username) {
    $user = UserFactory::fromUsername($username);

    if ($user->isMember()) {
      $user->incHits($user->id);
      $this->view->setVar('profile', $user);
    }

    return $user;
  }


  /**
   * @brief Returns `true` if the specified user matches the current one, `false` otherwise.
   * @param[in] User::IUser $user An user instance.
   * @retval bool
   */
  protected function isSameUser(IUser $user) {
    return ($user->isMember() && $this->user->match($user->getId()));
  }


  public function initialize() {
    // Prevents to call the method twice in case of forwarding.
    if ($this->dispatcher->isFinished() && $this->dispatcher->wasForwarded())
      return;

    parent::initialize();

    $this->resultsPerPage = $this->di['config']->application->postsPerPage;
  }


  public function afterExecuteRoute() {
    // Prevents to call the method twice in case of forwarding.
    if ($this->dispatcher->isFinished() && $this->dispatcher->wasForwarded())
      return;

    parent::afterExecuteRoute();
  }


  /**
   * @brief Displays the user's timeline.
   * @param[in] string $username A username.
   */
  public function indexAction($username) {
    $user = $this->getUser($username);

    // If the user doesn't exist, forward to 404.
    if (!$user->isMember()) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $opts = new ViewQueryOpts();

    // Paginates results.
    $startKey = isset($_GET['startkey']) ? (int)$_GET['startkey'] : Couch::WildCard();
    if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    $opts->doNotReduce()->setLimit($this->resultsPerPage+1)->reverseOrderOfResults()->setStartKey([$user->id, $startKey])->setEndKey([$user->id]);
    $rows = $this->couch->queryView("posts", "perDateByUser", NULL, $opts);

    $opts->reduce()->setStartKey([$user->id, Couch::WildCard()])->unsetOpt('startkey_docid');
    $count = $this->couch->queryView("posts", "perDateByUser", NULL, $opts)->getReducedValue();

    $entries = $this->getEntries(array_column($rows->asArray(), 'id'));

    if (count($entries) > $this->resultsPerPage) {
      $last = array_pop($entries);
      $this->view->setVar('nextPage', $this->buildPaginationUrlForCouch($last->publishedAt, $last->id));
    }

    $this->view->setVar('entries', $entries);
    $this->view->setVar('entriesCount', Helper\Text::formatNumber($count));
    $this->view->setVar('entriesLabel', 'contributi');
    $this->view->setVar('title', sprintf('%s timeline', $username));

    $this->view->pick('views/profile/timeline');
  }


  /**
   * @brief Displays the user's personal info.
   * @param[in] string $username A username.
   */
  public function aboutAction($username) {
    $user = $this->getUser($username);

    // If the user doesn't exist, forward to 404.
    if (!$user->isMember()) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $this->view->setVar('title', sprintf('About %s', $username));
    $this->view->pick('views/profile/about');
  }


  /**
   * @brief Displays the user's connections.
   * @param[in] string $username A username.
   * @param[in] string $filter (optional) Used to filter the connections.
   */
  public function connectionsAction($username, $filter = NULL) {
    $user = $this->getUser($username);

    // If the user doesn't exist, forward to 404.
    if (!$user->isMember()) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $this->view->setVar('title', sprintf('%s\'s connections', $username));
    $this->view->pick('views/profile/connections');
  }


  /**
   * @brief Displays the user's repositories.
   * @param[in] string $username A username.
   * @param[in] string $filter (optional) Filter between personal projects and forks.
   */
  public function repositoriesAction($username, $filter = NULL) {
    $user = $this->getUser($username);

    // If the user doesn't exist, forward to 404.
    if (!$user->isMember()) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $filters = ['personal-projects' => NULL, 'forks' => NULL];
    if (is_null($filter)) $filter = 'personal-projects';

    $filter = Helper\ArrayHelper::key($filter, $filters);
    if ($filter === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $this->dispatcher->setParam('filter', $filter);

    // Gets the service.
    $github = $this->di['github'];

    // Closure to compare consumer's name.
    $isGitHub = function ($var) {
      return $var[0] === 'github';
    };

    $repos = [];
    $logins = array_filter($user->logins->asArray(), $isGitHub);

    // Merges together the repositories of different users.
    foreach ($logins as $login) {
      $repos = array_merge($repos, $github->api('user')->repositories($login[4]));
    }

    $sorter = function ($one, $two) {
      return ($one['stargazers_count'] < $two['stargazers_count']);
    };

    usort($repos, $sorter);

    if ($filter === 'personal-projects') {

      $isPersonal = function ($var) {
        return $var['fork'] ? FALSE : TRUE;
      };

      $repos = array_filter($repos, $isPersonal);
    }
    else {

      $isFork = function ($var) {
        return $var['fork'] ? TRUE : FALSE;
      };

      $repos = array_filter($repos, $isFork);
    }

    // Converts ISO 8601 timestamp.
    $formatDate = function (&$value, $key) {
      $value['created_at'] = Helper\Time::when(date("U",strtotime($value['created_at'])));
    };
    array_walk($repos, $formatDate);

    $this->view->setVar('repos', $repos);
    $this->view->setVar('filters', $filters);
    $this->view->setVar('title', sprintf('%s\'s repositories', $username));
    $this->view->pick('views/profile/repositories');
  }


  /**
   * @brief Displays the user's activities.
   * @param[in] string $username A username.
   */
  public function activitiesAction($username) {
    $user = $this->getUser($username);

    // If the user doesn't exist, forward to 404.
    if (!$user->isMember()) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $this->view->setVar('title', sprintf('%s\'s activities', $username));
    $this->view->pick('views/profile/activities');
  }


  /**
   * @brief Let the current user to update his own personal info.
   * @param[in] string $username A username.
   */
  public function settingsAction($username) {
    $user = $this->getUser($username);
    if (!$this->isSameUser($user)) $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    // The validation object must be created in any case.
    $validation = new Validation();
    $this->view->setVar('validation', $validation);

    if ($this->request->isPost()) {

      try {
        $validation->setFilters("firstName", "trim");
        $validation->add("firstName", new PresenceOf(["message" => "First name is mandatory."]));

        $validation->setFilters("lastName", "trim");
        $validation->add("lastName", new PresenceOf(["message" => "Last name is mandatory."]));

        $group = $validation->validate($_POST);
        if (count($group) > 0) {
          throw new Exception\InvalidFieldException("Fields are incomplete or the entered values are invalid. The errors are reported in red under the respective entry fields.");
        }

        $this->user->firstName = $this->request->getPost('firstName');
        $this->user->lastName = $this->request->getPost('lastName');
        $this->user->gender = $this->request->getPost('gender');
        $this->user->birthday = strtotime($this->request->getPost('birthday'));
        //$this->user->about = $this->request->getPost('about');

        $this->user->save();

        $this->flash->success('Your personal information has been updated.');
      }
      catch (\Exception $e) {
        // Displays the error message.
        $this->flash->error($e->getMessage());
      }

    }
    else {
      $this->tag->setDefault("firstName", $this->user->firstName);
      $this->tag->setDefault("lastName", $this->user->lastName);
      $this->tag->setDefault("gender", $this->user->gender);
      $this->tag->setDefault("birthday", date('Y-m-d', $this->user->birthday));
      //$this->tag->setDefault("about", $this->user->about);
    }

    $this->view->setVar('title', sprintf('%s\'s settings', $this->user->username));
    $this->view->pick('views/profile/settings');
  }


  /**
   * @brief Let the user to update his own password.
   * @param[in] string $username A username.
   */
  public function passwordAction($username) {
    $user = $this->getUser($username);
    if (!$this->isSameUser($user)) $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    // The validation object must be created in any case.
    $validation = new Validation();
    $this->view->setVar('validation', $validation);

    if ($this->request->isPost()) {

      try {
        $validation->add("oldPassword", new PresenceOf(["message" => "La password è obbligatoria."]));
        $validation->add("newPassword", new Password());
        $validation->add('newPassword', new Confirmation(
          [
            'message' => "La password è diversa da quella di conferma.",
            'with' => 'confirmPassword'
          ]));

        $group = $validation->validate($_POST);
        if (count($group) > 0) {
          throw new Exception\InvalidFieldException("Fields are incomplete or the entered values are invalid. The errors are reported in red under the respective entry fields.");
        }

        $oldPassword = md5($this->request->getPost('oldPassword'));

        if ($this->user->password != $oldPassword)
          throw new Exception\WrongPasswordException("La password corrente è diversa da quella inserita. <a href=\"//".$this->domainName."/resetta-password/\">Hai dimenticato la password?</a>");

        $this->user->password = md5($this->request->getPost('newPassowrd'));

        $this->user->save();

        $this->flash->success('Congratulations, your username has been changed.');
      }
      catch (\Exception $e) {
        // Displays the error message.
        $this->flash->error($e->getMessage());
      }

    }

    $this->view->setVar('passwordMinLength', Password::MIN_LENGTH);
    $this->view->setVar('passwordMaxLength', Password::MAX_LENGTH);

    $this->view->setVar('title', sprintf('%s\'s settings', $this->user->username));
    $this->view->pick('views/profile/password');
  }


  /**
   * @brief Let the user to update his own username.
   * @param[in] string $username A username.
   */
  public function usernameAction($username) {
    $user = $this->getUser($username);
    if (!$this->isSameUser($user)) $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    // The validation object must be created in any case.
    $validation = new Validation();
    $this->view->setVar('validation', $validation);

    if ($this->request->isPost()) {

      try {
        $validation->setFilters("username", "trim");
        $validation->add("username", new Username());

        $group = $validation->validate($_POST);
        if (count($group) > 0) {
          throw new Exception\InvalidFieldException("Fields are incomplete or the entered values are invalid. The errors are reported in red under the respective entry fields.");
        }

        $this->user->username = $this->request->getPost('username');

        $this->user->save();

        $this->flash->success('Congratulations, your username has been changed.');
      }
      catch (\Exception $e) {
        // Displays the error message.
        $this->flash->error($e->getMessage());
      }

    }
    else {
      $this->tag->setDefault("username", $this->user->username);
    }

    $this->view->setVar('usernameMinLength', $this->config->application->usernameMinLength);
    $this->view->setVar('usernameMaxLength', $this->config->application->usernameMaxLength);

    $this->view->setVar('title', sprintf('%s\'s settings', $this->user->username));
    $this->view->pick('views/profile/username');
  }


  /**
   * @brief Let the user to manage his logins.
   * @param[in] string $username A username.
   */
  public function loginsAction($username) {
    $user = $this->getUser($username);
    if (!$this->isSameUser($user)) $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    if ($this->request->isPost()) {
      
      try {

        if ($this->request->getPost('removeLogin')) {
          $login = $this->request->getPost('removeLogin', 'string');

          if ($this->user->logins->exists($login)) {
            $this->user->logins->remove($login);
            $this->user->save();

            $this->flash->success("Congratulations, the social login has been removed from your account. The associated e-mail addresses haven't been removed.");
          }
          else
            throw new Exception\InvalidLoginException("La social login non è associata all'utente corrente.");
        }
      }
      catch (\Exception $e) {
        // Displays the error message.
        $this->flash->error($e->getMessage());
      }
      
    }

    $this->view->setVar('title', sprintf('%s\'s logins', $this->user->username));
    $this->view->pick('views/profile/logins');
  }


  /**
   * @brief Let the user to add or remove an e-mail address.
   * @param[in] string $username A username.
   */
  public function emailsAction($username) {
    $user = $this->getUser($username);
    if (!$this->isSameUser($user)) $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    // The validation object must be created in any case.
    $validation = new Validation();
    $this->view->setVar('validation', $validation);

    if ($this->request->isPost()) {

      try {

        // The user is trying to add an e-mail.
        if ($this->request->getPost('addEmail')) {
          if (count($this->user->emails) >= $this->di['config']->application->maxEmailsPerUser)
            throw new Exception\TooManyEmailsException("You have reached the maximum number of e-mails allowed.");

          $validation->setFilters("email", "trim");
          $validation->setFilters("email", "lower");
          $validation->add("email", new PresenceOf(["message" => "L'e-mail è obbligatoria."]));
          $validation->add("email", new Email(["message" => "L'e-mail non è valida."]));

          $group = $validation->validate($_POST);
          if (count($group) > 0) {
            throw new Exception\InvalidFieldException("I campi sono incompleti o i valori indicati non sono validi. Gli errori sono segnalati in rosso sotto ai rispettivi campi d'inserimento.");
          }

          $email = $this->request->getPost('email');

          if ($this->user->emails->exists($email))
            throw new Exception\InvalidEmailException("L'e-mail che stai tentando di aggiungere è già presente.");

          $opts = new ViewQueryOpts();
          $opts->setKey($email)->setLimit(1);

          $rows = $this->couch->queryView("members", "byEmail", NULL, $opts);

          if (!$rows->isEmpty())
            throw new Exception\InvalidEmailException("L'e-mail che stai tentando di aggiungere è già utilizzata da un altro utente.");

          $this->user->emails->add($email);

          $this->user->save();

          // Removes the email.
          unset($_POST["email"]);

          $this->flash->success('Congratulations, the e-mail has been added to your account. You should receive shortly an e-mail to verify your address.');
        }
        elseif ($this->request->getPost('removeEmail')) {
          $email = $this->request->getPost("removeEmail", "email");

          if ($this->user->emails->canRemove($email)) {
            $this->user->emails->remove($email);
            $this->user->save();

            // Removes the email.
            unset($_POST["email"]);

            $this->flash->success('Congratulations, the e-mail has been removed from your account.');
          }
          else
            throw new Exception\InvalidEmailException("L'e-mail non può essere rimossa.");
        }
        elseif ($this->request->getPost('resendVerificationEmail')) {
          $email = $this->request->getPost("resendVerificationEmail", "email");

          if (!$this->user->emails->exists($email))
            throw new Exception\InvalidEmailException("The e-mail you are trying to verify is not associated to your account.");
          elseif ($this->user->emails->isVerified($email))
            throw new Exception\InvalidEmailException("The e-mail is already verified.");

          // todo: Send the verification e-mail.

          // Removes the email.
          unset($_POST["email"]);

          $this->flash->success(sprintf('Congratulations, the verification e-mail has been sent to the following e-mail address: %s.', $email));
        }
        elseif ($this->request->getPost('setAsPrimaryEmail')) {
          $email = $this->request->getPost("setAsPrimaryEmail", "email");

          if (!$this->user->emails->exists($email))
            throw new Exception\InvalidEmailException("You are trying to set as primary an e-mail that doesn't belong to you.");
          elseif ($this->user->emails->isPrimary($email))
            throw new Exception\InvalidEmailException(sprintf("The address %s is already your primary e-mail.", $email));
          elseif (!$this->user->emails->isVerified($email))
            throw new Exception\InvalidEmailException("You are trying to set as primary an address hasn't been verified yet.");

          $this->user->emails->setPrimary($email);
          $this->user->save();

          // Removes the email.
          unset($_POST["email"]);

          $this->flash->success(sprintf('Congratulations, %s has been set as your primary e-mail.', $email));
        }

      }
      catch (\Exception $e) {
        // Displays the error message.
        $this->flash->error($e->getMessage());
      }

    }

    $this->view->setVar('title', sprintf('%s\'s e-mails', $username));
    $this->view->pick('views/profile/emails');
  }


  /**
   * @brief Let the user to update his privacy settings.
   * @param[in] string $username A username.
   */
  public function privacyAction($username) {
    $user = $this->getUser($username);
    if (!$this->isSameUser($user)) $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    if ($this->request->isPost()) {

    }
    else {
      $this->tag->setDefault("username", $this->user->username);
    }

    $this->view->setVar('title', sprintf('%s\'s privacy settings', $this->user->username));
    $this->view->pick('views/profile/privacy');
  }


  /**
   * @brief Displays the blacklisted members and let the user to add or remove an user from the blacklist.
   * @param[in] string $username A username.
   */
  public function blacklistAction($username) {
    $user = $this->getUser($username);
    if (!$this->isSameUser($user)) $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    // The validation object must be created in any case.
    $validation = new Validation();
    $this->view->setVar('validation', $validation);

    if ($this->request->isPost()) {

      try {

        // The user is trying to add a member to the blacklist.
        if ($this->request->getPost('addMember')) {
          $validation->setFilters("username", "trim");
          $validation->add("username", new PresenceOf(["message" => "Lo username è obbligatorio."]));

          $group = $validation->validate($_POST);
          if (count($group) > 0) {
            throw new Exception\InvalidFieldException("I campi sono incompleti o i valori indicati non sono validi. Gli errori sono segnalati in rosso sotto ai rispettivi campi d'inserimento.");
          }

          $username = $this->request->getPost('username');

          if ($username === $this->user->username)
            throw new Exception\UserMismatchException("Non puoi aggiungere te stesso alla blacklist.");

          $opts = new ViewQueryOpts();
          $opts->setKey($username)->setLimit(1);

          $rows = $this->couch->queryView("members", "byUsername", NULL, $opts);

          if ($rows->isEmpty())
            throw new Exception\UserNotFoundException("Non esiste nessun utente con lo username inserito.");

          $member = $this->couch->getDoc(Couch::STD_DOC_PATH, $rows->asArray()[0]['id']);

          if ($this->user->blacklist->exists($member))
            throw new Exception\UserMismatchException("L'utente che stai cercando di aggiungere è già presente nella tua blacklist.");

          if ($this->user->friends->exists($member))
            throw new Exception\UserNotFoundException("Non puoi aggiungere un tuo amico alla blacklist, prima rimuovilo dagli amici, poi aggiungilo alla blacklist.");

          if ($member->roles->isSuperior(new ModeratorRole()))
            throw new Exception\InvalidEmailException(sprintf("Siamo spiacenti ma non puoi aggiungere questo utente alla blacklist, in quanto moderatore o amministratore della comunità. Se sei oggetto di molestie contattaci via e-mail all'indirizzo %s.", $this->config->application->supportEmail));

          $this->user->blacklist->add($member);

          $this->user->save();

          // Removes the username.
          unset($_POST["username"]);

          $this->flash->success('Congratulations, the user has been to your blacklist.');
        }
        elseif ($this->request->getPost('removeMember')) {
          $username = $this->request->getPost("removeMember", "username");

          if ($this->user->blacklist->exists($username)) {
            $this->user->blacklist->remove($username);
            $this->user->save();

            // Removes the email.
            unset($_POST["username"]);

            $this->flash->success('Congratulations, the user has been removed from your blacklist.');
          }
          else
            throw new Exception\UserNotFoundException("L'utente non è presente nella tua blacklist.");
        }

      }
      catch (\Exception $e) {
        // Displays the error message.
        $this->flash->error($e->getMessage());
      }

    }

    $this->view->setVar('title', sprintf('%s\'s blacklist', $username));
    $this->view->pick('views/profile/blacklist');
  }

} 
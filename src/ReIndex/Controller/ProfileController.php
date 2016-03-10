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
use ReIndex\Exception\InvalidFieldException;
use ReIndex\Validator\Password;
use ReIndex\Validator\Username;

use Phalcon\Mvc\View;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\PresenceOf;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;


/**
 * @brief Member's profile controller.
 * @nosubgrouping
 */
class ProfileController extends ListController {


  /**
   * @brief Given a username returns the correspondent user.
   */
  protected function getUser($username) {
    $this->log->addDebug(sprintf('Username: %s', $username));

    $user = UserFactory::fromUsername($username);

    // If the user doesn't exist, forward to 404.
    if (!$user->isMember()) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $user->incHits($this->user->id);

    $this->view->setVar('profile', $user);

    return $user;
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

    $this->view->setVar('title', sprintf('About %s', $username));
    $this->view->pick('views/profile/about');
  }


  /**
   * @brief Displays the user's connections.
   * @param[in] string $filter (optional) Used to filter the connections.
   */
  public function connectionsAction($username, $filter = NULL) {
    $user = $this->getUser($username);

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
    $logins = array_filter($user->getLogins(), $isGitHub);

    // Merges together the repositories of different users.
    foreach ($logins as $login) {
      $repos = array_merge($repos, $github->api('user')->repositories($login[1]));
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

    $this->view->setVar('title', sprintf('%s\'s activities', $username));
    $this->view->pick('views/profile/activities');
  }


  /**
   * @brief Let the current user to update his own personal info.
   * @param[in] string $username A username.
   */
  public function settingsAction($username) {
    $user = $this->getUser($username);
    if (!$this->user->match($user->id)) $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    // The validation object must be created in any case.
    $validation = new Helper\ValidationHelper();
    $this->view->setVar('validation', $validation);

    if ($this->request->isPost()) {

      try {
        $validation->setFilters("firstName", "trim");
        $validation->add("firstName", new PresenceOf(["message" => "First name is mandatory."]));

        $validation->setFilters("lastName", "trim");
        $validation->add("lastName", new PresenceOf(["message" => "Last name is mandatory."]));

        $group = $validation->validate($_POST);
        if (count($group) > 0) {
          throw new InvalidFieldException("Fields are incomplete or the entered values are invalid. The errors are reported in red under the respective entry fields.");
        }

        // Filters only the messages generated for the field 'name'.
        /*foreach ($validation->getMessages()->filter('email') as $message) {
          $this->flash->notice($message->getMessage());
          break;
        }*/

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
      $this->tag->setDefault("about", $this->user->about);
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
    if (!$this->user->match($user->id)) $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    // The validation object must be created in any case.
    $validation = new Helper\ValidationHelper();
    $this->view->setVar('validation', $validation);

    if ($this->request->isPost()) {

      try {
        $validation->add("password", new Password());
        $validation->add('password', new Confirmation(
          [
            'message' => "La password è diversa da quella di conferma.",
            'with' => 'confirmPassword'
          ]));

        $group = $validation->validate($_POST);
        if (count($group) > 0) {
          throw new InvalidFieldException("Fields are incomplete or the entered values are invalid. The errors are reported in red under the respective entry fields.");
        }

        $password = md5($this->request->getPost('newPassword'));
        $this->user->password = $password;

        $this->user->save();

        $this->flash->success('Congratulations, your username has been changed.');
      }
      catch (\Exception $e) {
        // Displays the error message.
        $this->flash->error($e->getMessage());
      }

    }

    $this->view->setVar('title', sprintf('%s\'s settings', $this->user->username));
    $this->view->pick('views/profile/password');
  }


  /**
   * @brief Let the user to update his own username.
   * @param[in] string $username A username.
   */
  public function usernameAction($username) {
    $user = $this->getUser($username);
    if (!$this->user->match($user->id)) $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    // The validation object must be created in any case.
    $validation = new Helper\ValidationHelper();
    $this->view->setVar('validation', $validation);

    if ($this->request->isPost()) {

      try {
        $validation->setFilters("username", "trim");
        $validation->add("username", new Username());

        $group = $validation->validate($_POST);
        if (count($group) > 0) {
          throw new InvalidFieldException("Fields are incomplete or the entered values are invalid. The errors are reported in red under the respective entry fields.");
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
    if (!$this->user->match($user->id)) $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    if ($this->request->isPost()) {
    }
    else {
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
    if (!$this->user->match($user->id)) $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    if ($this->request->isPost()) {
    }
    else {
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
    if (!$this->user->match($user->id)) $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    if ($this->request->isPost()) {

    }
    else {
      $this->tag->setDefault("username", $this->user->username);
    }

    $this->view->setVar('title', sprintf('%s\'s privacy settings', $this->user->username));
    $this->view->pick('views/profile/privacy');
  }

} 
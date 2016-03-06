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


  public function aboutAction($username) {
    $user = $this->getUser($username);

    $this->view->setVar('title', sprintf('About %s', $username));
    $this->view->pick('views/profile/about');
  }


  public function connectionsAction($username, $filter = NULL) {
    $user = $this->getUser($username);

    $this->view->setVar('title', sprintf('%s\'s connections', $username));
    $this->view->pick('views/profile/connections');
  }


  public function repositoriesAction($username, $filter = NULL) {
    $user = $this->getUser($username);

    $filters = ['personal' => NULL, 'forks' => NULL];
    if (is_null($filter)) $filter = 'insertion-date';

    $filter = Helper\ArrayHelper::key($filter, $filters);
    if ($filter === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $logins = $user->getLogins();

    $github = $this->di['github'];
    $repos = $github->api('user')->repositories('dedalozzo');
    $this->view->setVar('title', sprintf('%s\'s projects', $username));
    $this->view->pick('views/profile/projects');
  }


  public function activitiesAction($username) {
    $user = $this->getUser($username);

    $this->view->setVar('title', sprintf('%s\'s activities', $username));
    $this->view->pick('views/profile/activities');
  }


  public function settingsAction($username) {
    $user = $this->getUser($username);

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

        $firstName = $this->request->getPost('firstName');
        $lastName = $this->request->getPost('lastName');
      }
      catch (\Exception $e) {
        // Displays the error message.
        $this->flash->error($e->getMessage());
      }

    }
    else {
      $this->tag->setDefault("firstName", $user->firstName);
      $this->tag->setDefault("lastName", $user->lastName);
      $this->tag->setDefault("gender", $user->gender);
      $this->tag->setDefault("birthday", $user->birthday);
      $this->tag->setDefault("about", $user->about);
    }

    $this->view->setVar('title', sprintf('%s\'s settings', $username));
    $this->view->pick('views/profile/settings');
  }


  public function passwordAction($username) {
    $user = $this->getUser($username);

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

        // Filters only the messages generated for the field 'name'.
        /*foreach ($validation->getMessages()->filter('email') as $message) {
          $this->flash->notice($message->getMessage());
          break;
        }*/

        $oldPassword = $this->request->getPost('oldPassword');
        $newPassword = $this->request->getPost('newPassword');
      }
      catch (\Exception $e) {
        // Displays the error message.
        $this->flash->error($e->getMessage());
      }

    }
    else {
      $this->tag->setDefault("oldPassword", $user->firstName);
      $this->tag->setDefault("newPassword", $user->lastName);
      $this->tag->setDefault("confirmPassword", $user->gender);
    }

    $this->view->setVar('title', sprintf('%s\'s settings', $username));
    $this->view->pick('views/profile/password');
  }


  public function usernameAction($username) {
    $user = $this->getUser($username);

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

        // Filters only the messages generated for the field 'name'.
        /*foreach ($validation->getMessages()->filter('email') as $message) {
          $this->flash->notice($message->getMessage());
          break;
        }*/

        $username = $this->request->getPost('username');
      }
      catch (\Exception $e) {
        // Displays the error message.
        $this->flash->error($e->getMessage());
      }

    }
    else {
      $this->tag->setDefault("username", $user->username);
    }

    $this->view->setVar('title', sprintf('%s\'s settings', $username));
    $this->view->pick('views/profile/username');
  }


  public function loginsAction($username) {
    $user = $this->getUser($username);

    if ($this->request->isPost()) {
    }
    else {
    }

    $this->view->setVar('title', sprintf('%s\'s logins', $username));
    $this->view->pick('views/profile/logins');
  }


  public function emailsAction($username) {
    $user = $this->getUser($username);

    if ($this->request->isPost()) {
    }
    else {
    }

    $this->view->setVar('title', sprintf('%s\'s e-mails', $username));
    $this->view->pick('views/profile/emails');
  }


  public function privacyAction($username) {
    $user = $this->getUser($username);

    if ($this->request->isPost()) {

    }
    else {
      $this->tag->setDefault("username", $user->username);
    }

    $this->view->setVar('title', sprintf('%s\'s privacy settings', $username));
    $this->view->pick('views/profile/privacy');
  }

} 
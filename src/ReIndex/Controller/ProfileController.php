<?php

/**
 * @file ProfileController.php
 * @brief This file contains the ProfileController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Controller;


use ReIndex\Doc\Post;
use ReIndex\Doc\Member;
use ReIndex\Factory\UserFactory;
use ReIndex\Exception;
use ReIndex\Security\Role\ModeratorRole;
use ReIndex\Validation;
use ReIndex\Validator\Password;
use ReIndex\Validator\Username;

use Phalcon\Mvc\View;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use Daikengo\User\IUser;

use ToolBag\Helper;


/**
 * @brief Member's profile controller.
 * @nosubgrouping
 */
final class ProfileController extends ListController {


  /**
   * @brief Given a username returns the correspondent user.
   * @param[in] string $username A username.
   * @retval User::IUser
   */
  protected function getUser($username) {
    $user = UserFactory::fromUsername($username);

    if ($user->isMember()) {
      // todo: Generates a view for the current user.
      //$user->hits->inc($user->id);
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
   * @param[in] int $year Used to jump to the posts published during that year.
   */
  public function indexAction($username, $year = NULL) {
    $user = $this->getUser($username);

    // If the user doesn't exist, forward to 404.
    if (!$user->isMember()) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    /*
    $opts = new ViewQueryOpts();

    // Paginates results.
    $startKey = isset($_GET['startkey']) ? (int)$_GET['startkey'] : Couch::WildCard();
    if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    $opts->doNotReduce()->setLimit($this->resultsPerPage+1)->reverseOrderOfResults()->setStartKey([$user->id, $startKey])->setEndKey([$user->id]);
    $rows = $this->couch->queryView("posts", "perDateByUser", NULL, $opts);

    $opts->reduce()->setStartKey([$user->id, Couch::WildCard()])->unsetOpt('startkey_docid');
    $count = $this->couch->queryView("posts", "perDateByUser", NULL, $opts)->getReducedValue();

    $posts = Post::collect(array_column($rows->asArray(), 'id'));

    if (count($posts) > $this->resultsPerPage) {
      $last = array_pop($posts);
      $this->view->setVar('nextPage', $this->buildPaginationUrlForCouch($last->publishedAt, $last->id));
    }

    $this->view->setVar('posts', $posts);
    $this->view->setVar('entriesCount', Helper\TextHelper::formatNumber($count));
    */
    $this->view->setVar('entriesLabel', 'articles');
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

    $opts = new ViewQueryOpts();
    $opts->setLimit($this->resultsPerPage+1);

    // Paginates results.
    $startKey = isset($_GET['startkey']) ? $_GET['startkey'] : chr(0);
    $opts->setStartKey([TRUE, $user->id, $startKey])->setEndKey([TRUE, $user->id]);
    if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    // friendships/relations/view
    $rows = $this->couch->queryView('friendships', 'relations', 'view', NULL, $opts)->asArray();

    $members = Member::collect(array_column($rows, 'id'));

    if (count($members) > $this->resultsPerPage) {
      $last = array_pop($members);
      $this->view->setVar('nextPage', $this->buildPaginationUrlForCouch($last->username, $last->id));
    }

    $this->view->setVar('members', $members);

    $this->view->setVar('title', sprintf('%s\'s connections', $username));
    $this->view->pick('views/profile/connections');
  }


  /**
   * @brief Displays the user's repositories.
   * @param[in] string $username A username.
   * @param[in] string $filter (optional) Filter between personal projects and forks.
   */
  public function repositoriesAction($username, $filter = 'personal-projects') {
    $user = $this->getUser($username);

    // If the user doesn't exist, forward to 404.
    if (!$user->isMember()) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $filters = ['personal-projects' => NULL, 'forks' => NULL];

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
      $value['created_at'] = Helper\TimeHelper::when(date("U", strtotime($value['created_at'])));
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
  public function infoAction($username) {
    $user = $this->getUser($username);

    if ($this->user->isGuest() or !$this->user->match($user->id)) $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    // The validation object must be created in any case.
    $validation = new Validation();
    $this->view->setVar('validation', $validation);

    if ($this->request->isPost()) {

      try {
        $validation->setFilters("firstName", "trim");
        $validation->add("firstName", new PresenceOf(["message" => "First name is required."]));

        $validation->setFilters("lastName", "trim");
        $validation->add("lastName", new PresenceOf(["message" => "Last name is required."]));

        $validation->run($_POST);

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

    $this->view->setVar('profile', $this->user);
    $this->view->setVar('title', sprintf('%s\'s settings', $this->user->username));
    $this->view->pick('views/profile/settings/info');
  }


  /**
   * @brief Validates the the password form's input fields.
   * @param Validation $validation A validation component.
   */
  private function passwordValidation(Validation $validation) {
    $validation->add("oldPassword", new PresenceOf(["message" => "The password is required."]));
    $validation->add("newPassword", new Password());
    $validation->add('newPassword', new Confirmation(
      [
        'message' => "Password is different from the confirm password.",
        'with' => 'confirmPassword'
      ]));

    $validation->run($_POST);
  }


  /**
   * @brief Let the user to update his own password.
   * @param[in] string $username A username.
   */
  public function passwordAction($username) {
    $user = $this->getUser($username);

    if ($this->user->isGuest() or !$this->user->match($user->id)) $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    // The validation object must be created in any case.
    $validation = new Validation();
    $this->view->setVar('validation', $validation);

    if ($this->request->isPost()) {

      try {
        $this->passwordValidation($validation);

        $oldPassword = md5($this->request->getPost('oldPassword'));

        if ($this->user->password != $oldPassword)
          throw new Exception\WrongPasswordException("The current password is wrong. <a href=\"//".$this->domainName."/resetpasswd/\">Did you forget it?</a>");

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

    $this->view->setVar('profile', $this->user);
    $this->view->setVar('title', sprintf('%s\'s settings', $this->user->username));
    $this->view->pick('views/profile/settings/password');
  }


  /**
   * @brief Let the user to update his own username.
   * @param[in] string $username A username.
   */
  public function usernameAction($username) {
    $user = $this->getUser($username);

    if ($this->user->isGuest() or !$this->user->match($user->id)) $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    // The validation object must be created in any case.
    $validation = new Validation();
    $this->view->setVar('validation', $validation);

    if ($this->request->isPost()) {

      try {
        $validation->setFilters("username", "trim");
        $validation->add("username", new Username());
        $validation->run($_POST);

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

    $this->view->setVar('profile', $this->user);
    $this->view->setVar('title', sprintf('%s\'s settings', $this->user->username));
    $this->view->pick('views/profile/settings/username');
  }


  /**
   * @brief Let the user to manage his logins.
   * @param[in] string $username A username.
   */
  public function loginsAction($username) {
    $user = $this->getUser($username);

    if ($this->user->isGuest() or !$this->user->match($user->id)) $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

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

    $this->view->setVar('profile', $this->user);
    $this->view->setVar('title', sprintf('%s\'s logins', $this->user->username));
    $this->view->pick('views/profile/settings/logins');
  }


  /**
   * @brief Let the user to add or remove an e-mail address.
   * @param[in] string $username A username.
   */
  public function emailsAction($username) {
    $user = $this->getUser($username);

    if ($this->user->isGuest() or !$this->user->match($user->id)) $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    // The validation object must be created in any case.
    $validation = new Validation();
    $this->view->setVar('validation', $validation);

    if ($this->request->isPost()) {

      try {

        // The user is trying to add an e-mail.
        if ($this->request->getPost('addEmail')) {
          if (count($this->user->emails) >= $this->di['config']->application->maxEmailsPerUser)
            throw new Exception\TooManyEmailsException("You have reached the maximum number of e-mails allowed.");

          $validation->setFilters("email", ["email", "lower"]);
          $validation->add("email", new PresenceOf(["message" => "The e-mail is required."]));
          $validation->add("email", new Email(["message" => "Please, enter a valid e-mail."]));
          $validation->run($_POST);

          $email = $this->request->getPost('email', ["email", "lower"]);

          if ($this->user->emails->exists($email))
            throw new Exception\InvalidEmailException("Sorry, the e-mail is already associated with your account.");

          $opts = new ViewQueryOpts();
          $opts->setKey($email)->setLimit(1);

          // members/byEmail/view
          $rows = $this->couch->queryView("members", 'byEmail', 'view', NULL, $opts);

          if (!$rows->isEmpty())
            throw new Exception\InvalidEmailException("Sorry, the e-mail is already in use by another user.");

          $this->user->emails->add($email);

          $this->user->save();

          // Removes the email.
          unset($_POST["email"]);

          $this->flash->success(sprintf('Congratulations, <b>%s</b> has been added to your account. You should receive shortly an e-mail to verify your address.', $email));
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
            throw new Exception\InvalidEmailException("The e-mail cannot be removed.");
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

          $this->flash->success(sprintf('Congratulations, the verification e-mail has been sent to the following e-mail address: <b>%s</b>.', $email));
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

          $this->flash->success(sprintf('Congratulations, <b>%s</b> has been set as your primary e-mail.', $email));
        }

      }
      catch (\Exception $e) {
        // Displays the error message.
        $this->flash->error($e->getMessage());
      }

    }

    $this->view->setVar('profile', $this->user);
    $this->view->setVar('title', sprintf('%s\'s e-mails', $this->user->username));
    $this->view->pick('views/profile/settings/emails');
  }


  /**
   * @brief Let the user to update his privacy settings.
   * @param[in] string $username A username.
   */
  public function privacyAction($username) {
    $user = $this->getUser($username);

    if ($this->user->isGuest() or !$this->user->match($user->id)) $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    if ($this->request->isPost()) {

    }
    else {
      $this->tag->setDefault("username", $this->user->username);
    }

    $this->view->setVar('profile', $this->user);
    $this->view->setVar('title', sprintf('%s\'s privacy settings', $this->user->username));
    $this->view->pick('views/profile/settings/privacy');
  }


  /**
   * @brief Displays the blacklisted members and let the user to add or remove an user from the blacklist.
   * @param[in] string $username A username.
   */
  public function blacklistAction($username) {
    $user = $this->getUser($username);

    if ($this->user->isGuest() or !$this->user->match($user->id)) $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    // The validation object must be created in any case.
    $validation = new Validation();
    $this->view->setVar('validation', $validation);

    if ($this->request->isPost()) {

      try {

        // The user is trying to add a member to the blacklist.
        if ($this->request->getPost('addMember')) {
          $validation->setFilters("nickname", "trim");
          $validation->add("nickname", new PresenceOf(["message" => "The nickname is required."]));
          $validation->run($_POST);

          $nickname = $this->request->getPost('nickname');

          if ($nickname === $this->user->username)
            throw new Exception\UserMismatchException("Mate, are you trying to blacklist yourself?");

          $member = UserFactory::fromUsername($nickname);

          if ($member->isGuest())
            throw new Exception\UserNotFoundException("The user does not exist.");

          if ($this->user->blacklist->exists($member))
            throw new Exception\UserMismatchException("Mate, you are trying to add a user that is already in your blacklist.");

          if ($this->user->friends->exists($member))
            throw new Exception\UserNotFoundException("You cannot blacklist a friend. Unfriend him, then you will be able to add him to the blacklist.");

          if ($member->roles->areSuperiorThan(new ModeratorRole()))
            throw new Exception\InvalidEmailException(sprintf("We are afraid but you cannot add to the blacklist this user, since he is a moderator or an administrator of this community. If you are subject to harassment by a moderator contact us via e-mail all'indirizzo %s.", $this->config->application->supportEmail));

          $this->user->blacklist->add($member);

          $this->user->save();

          // Removes the username.
          unset($_POST["nickname"]);

          $this->flash->success(sprintf('Congratulations, the user <b>%s</b> has been added to your blacklist.', $member->username));
        }
        elseif ($this->request->getPost('removeMember')) {
          $nickname = $this->request->getPost("removeMember", "string");

          $member = UserFactory::fromUsername($nickname);

          if ($member->isMember() && $this->user->blacklist->exists($member)) {
            $this->user->blacklist->remove($member);
            $this->user->save();

            // Removes the username.
            unset($_POST["nickname"]);

            $this->flash->success('Congratulations, the user has been removed from your blacklist.');
          }
        }

      }
      catch (\Exception $e) {
        // Displays the error message.
        $this->flash->error($e->getMessage());
      }

    }

    $this->view->setVar('profile', $this->user);
    $this->view->setVar('title', sprintf('%s\'s blacklist', $this->user->username));
    $this->view->pick('views/profile/settings/blacklist');
  }

} 
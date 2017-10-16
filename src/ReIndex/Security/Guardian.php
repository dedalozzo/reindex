<?php

/**
 * @file Security/Guardian.php
 * @brief This file contains the Guardian class.
 * @details
 * @author Filippo F. Fadda
 */


//! Classes to handle the security
namespace ReIndex\Security;


Use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use ReIndex\Factory\UserFactory;
use ReIndex\Security\Role as ReIndexRole;

use Daikengo\Role as BaseRole;
use Daikengo\Role\IRole;

use ToolBag\Extension\TProperty;

use Monolog\Logger;

use Phalcon\Config;
use Phalcon\Di;


/**
 * @brief This class is responsible to ensure an user has the ability to perform any operation.
 * @nosubgrouping
 */
final class Guardian {
  use TProperty;

  private static $initialized = FALSE;

  protected static $user = NULL; // Current user.
  protected static $roles = [];  // Available roles.

  /**
   * @var Couch $couch
   */
  protected $couch;

  /**
   * @var Logger $log
   */
  protected $log;


  /**
   * @brief Constructor.
   * @param[in] \Phalcon\Config $config The configuration object.
   * @param[in] \Phalcon\Di $di The Dependency Injection component.
   */
  public function __construct(Config $config, Di $di) {
    $this->log = $di['log'];
    $this->couch = $di['couchdb'];

    if (!self::$initialized) {
      self::$initialized = TRUE;
      self::$user = UserFactory::fromCookie();

      $this->loadRole(new BaseRole\SupervisorRole());
      $this->loadRole(new BaseRole\AdminRole());
      $this->loadRole(new ReIndexRole\ModeratorRole());
      $this->loadRole(new ReIndexRole\ReviewerRole());
      $this->loadRole(new ReIndexRole\EditorRole());
      $this->loadRole(new ReIndexRole\TrustedRole());
      $this->loadRole(new BaseRole\MemberRole());

      // Special role.
      $this->loadRole(new BaseRole\DeveloperRole());
    }
  }


  /**
   * @brief Returns `true` in case the username is taken, `false` otherwise.
   * @param[in] string $username The username.
   * @retval bool
   */
  public function isTaken($username) {
    $opts = new ViewQueryOpts();
    $opts->setLimit(1)->setKey($username);

    // members/byUsername/view
    $result = $this->couch->queryView('members', 'byUsername', 'view', NULL, $opts);

    return ($result->isEmpty()) ? FALSE : TRUE;
  }


  /**
   * @brief Returns the logged in user.
   * @retval IUser
   */
  public function getUser() {
    return self::$user;
  }


  /**
   * @brief Gets the available roles.
   */
  public function allRoles() {
    return self::$roles;
  }


  /**
   * @brief Unloads all the roles.
   */
  public function resetRoles() {
    self::$roles = [];
  }


  /**
   * @brief Loads the given role.
   * @param[in] Role::IRole $role An instance of a class that implements IRole.
   */
  public function loadRole(IRole $role) {
    if (array_key_exists($role->getName(), self::$roles))
      throw new \RuntimeException(sprintf("The '%s' role already exists.", $role->getName()));

    self::$roles[$role->getName()] = $role;
  }


  /**
   * @brief Returns `true` if the role is already present, `false` otherwise.
   * @param[in] string $name A role's name.
   * @retval bool
   */
  public function roleExists($name) {
    return isset(self::$roles[$name]);
  }


  /**
   * @brief Returns the role identified by the given name.
   * @param[in] string $name A role's name.
   * @warning This method doesn't check if the role exists, so be careful.
   * @retval bool
   */
  public function getRole($name) {
    return self::$roles[$name];
  }

}
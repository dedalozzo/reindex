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
use EoC\Extension\TProperty;
use EoC\Opt\ViewQueryOpts;

use ReIndex\Factory\UserFactory;
use ReIndex\Security\Role\IRole;

use Monolog\Logger;


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


  public function __construct($config, $di) {
    $this->log = $di['log'];
    $this->couch = $di['couchdb'];

    if (!self::$initialized) {
      self::$initialized = TRUE;
      self::$user = UserFactory::fromCookie();

      $this->loadRole(new Role\SupervisorRole());
      $this->loadRole(new Role\AdminRole());
      $this->loadRole(new Role\ModeratorRole());
      $this->loadRole(new Role\ReviewerRole());
      $this->loadRole(new Role\EditorRole());
      $this->loadRole(new Role\TrustedRole());
      $this->loadRole(new Role\MemberRole());

      // Special role.
      $this->loadRole(new Role\DeveloperRole());
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
    unset(self::$roles);
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
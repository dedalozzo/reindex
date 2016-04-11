<?php

/**
 * @file Security/Guardian.php
 * @brief This file contains the Guardian class.
 * @details
 * @author Filippo F. Fadda
 */


//! Classes to handle the security
namespace ReIndex\Security;


use EoC\Extension;
use EoC\Opt\ViewQueryOpts;

use ReIndex\Factory\UserFactory;
use ReIndex\Exception\NotEnoughPrivilegesException;
use ReIndex\Security\User\IUser;
use ReIndex\Security\Role\IRole;


/**
 * @brief This class is responsible to ensure an user has the ability to perform any operation.
 * @nosubgrouping
 */
class Guardian {
  use Extension\TProperty;

  private static $initialized = FALSE;

  protected static $user = NULL; // Current user.
  protected static $roles = []; // Available roles.

  private $log; // Monolog instance.
  private $couch; // CouchDB client instance.


  public function __construct($config, $di) {
    $this->log = $di['log'];
    $this->couch = $di['couchdb'];

    if (!self::$initialized) {
      self::$initialized = TRUE;
      self::$user = UserFactory::fromCookie();

      $this->loadRole(new Role\SupervisorRole());
      $this->loadRole(new Role\DeveloperRole());
      $this->loadRole(new Role\AdminRole());
      $this->loadRole(new Role\ModeratorRole());
      $this->loadRole(new Role\ReviewerRole());
      $this->loadRole(new Role\EditorRole());
      $this->loadRole(new Role\TrustedRole());
      $this->loadRole(new Role\MemberRole());
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

    $result = $this->couch->queryView("members", "byUsername", NULL, $opts);

    return ($result->isEmpty()) ? FALSE : TRUE;
  }


  /**
   * @brief Impersonates the given user.
   * @param[in] IUser $user An user instance.
   */
  public function impersonate(IUser $user) {
    if ($this->canImpersonate($user))
      $this->user = $user;
    else
      throw new NotEnoughPrivilegesException('Non hai sufficienti privilegi per impersonare un altro utente.');
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
  public function getRoles() {
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
   * @param[in] ReIndex::Security::Role::IRole $role An instance of a class that implements IRole.
   */
  public function loadRole(IRole $role) {
    $class = get_class($role);

    if (array_key_exists($class, self::$roles))
      throw new \Exception(sprintf("The '%s' role already exists.", $role->getName()));

    self::$roles[$class] = $role;
  }

}
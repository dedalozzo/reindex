<?php

/**
 * @file RoleCollection.php
 * @brief This file contains the RoleCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Security\User\IUser;
use ReIndex\Security\Role\IRole;
use ReIndex\Security\Role\Permission\Role\GrantPermission;
use ReIndex\Exception\AccessDeniedException;


/**
 * @brief This class is used to represent a collection of roles.
 * @nosubgrouping
 */
final class RoleCollection extends MetaCollection {

  /**
   * @var IUser $user
   */
  protected $user;


  /**
   * @brief Creates a new collection of roles.
   * @param[in] string $name Collection's name.
   * @param[in] array $meta Array of metadata.
   */
  public function __construct($name, array &$meta) {
    parent::__construct($name, $meta);
    $this->user = $this->di['guardian']->getUser();
  }


  /**
   * @brief Grants the specified role to the current member.
   * @details The algorithm discards any role when a more important one has been granted to the member. That means you
   * can't add the Moderator role to an Admin, etc. You can also grant multiple roles to a member, to assign special
   * permissions.
   * @param[in] IRole $role A role object.
   */
  public function grant(IRole $role) {
    if (!$this->user->has(new GrantPermission($role)))
      throw new AccessDeniedException('Not enough privileges to grant the role.');

    // Checks if the same role has been already assigned to the member.
    if ($this->exists($role))
      return;

    $roles = $this->meta[$this->name];

    foreach ($roles as $name => $class) {

      if (is_subclass_of($class, get_class($role))) {
        // If a subclass of `$role` exists for the current collection then the function returns, because a more
        // important role has been already assigned to the member.
        return;
      }
      elseif (is_subclass_of($role, $class, FALSE)) {
        // If `$role` is an instance of a subclass of any role previously assigned to the member that means the new role
        // is more important and the one already assigned must be removed.
        unset($this->meta[$this->name][$name]);
      }

    }

    // Uses as key the role's name and as value its class.
    $this->meta[$this->name][$role->getName()] = get_class($role);
  }


  /**
   * @brief Revokes the specified role for the current member.
   * @param[in] IRole $role A role object.
   */
  public function revoke(IRole $role) {
    if (!$this->user->has(new GrantPermission($role)))
      throw new AccessDeniedException('Not enough privileges to revoke the role.');

    if ($this->exists($role))
      unset($this->meta[$this->name][$role->getName()]);
  }


  /**
   * @brief Returns `true` if the role is already present, `false` otherwise.
   * @param[in] IRole $role A role object.
   * @retval bool
   */
  public function exists(IRole $role) {
    return isset($this->meta[$this->name][$role->getName()]);
  }


  /**
   * @brief Returns `true` if at least one of the role associated to the current user is an instance of subclass (or an
   * instance of the same class) of the specified role object, `false` otherwise.
   * @param[in] IRole $role A role object.
   * @param[in] bool $orEqual (optional) When `false` doesn't check if the role is an instance of the same class.
   */
  public function areSuperiorThan(IRole $role, $orEqual = TRUE) {
    $result = FALSE;

    $roles = $this->meta[$this->name];

    $roleClass = get_class($role);

    foreach ($roles as $name => $class) {

      if (is_subclass_of($class, $roleClass) or ($orEqual && ($class === $roleClass))) {
        $result = TRUE;
        break;
      }

    }

    return $result;
  }

}
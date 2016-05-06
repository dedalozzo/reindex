<?php

/**
 * @file RoleCollection.php
 * @brief This file contains the RoleCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Security\Role\IRole;
use ReIndex\Security\Role\AdminRole\GrantRolePermission;
use ReIndex\Exception\NotEnoughPrivilegesException;


/**
 * @brief This class is used to represent a collection of roles.
 * @nosubgrouping
 */
class RoleCollection extends MetaCollection {

  const NAME = "roles";

  protected $user;  // Stores the current user.


  /**
   * @brief Creates a new collection of roles.
   * @param[in] array $meta Member's array of metadata.
   */
  public function __construct(array &$meta) {
    parent::__construct($meta);
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
    if (!$this->user->has(new GrantRolePermission($role)))
      throw new NotEnoughPrivilegesException('Not enough privileges to grant the role.');

    // Checks if the same role has been already assigned to the member.
    if ($this->exists($role))
      return;

    $roles = $this->meta[static::NAME];

    foreach ($roles as $name => $class) {

      if (is_subclass_of($class, get_class($role))) {
        // If a subclass of `$role` exists for the current collection then the function returns, because a more
        // important role has been already assigned to the member.
        return;
      }
      elseif (is_subclass_of($role, $class, FALSE)) {
        // If `$role` is an instance of a subclass of any role previously assigned to the member that means the new role
        // is more important and the one already assigned must be removed.
        unset($this->meta[static::NAME][$name]);
      }

    }

    // Uses as key the role's name and as value its class.
    $this->meta[static::NAME][$role->getName()] = get_class($role);
  }


  /**
   * @brief Revokes the specified role for the current member.
   * @param[in] IRole $role A role object.
   */
  public function revoke(IRole $role) {
    if (!$this->user->has(new GrantRolePermission($role)))
      throw new NotEnoughPrivilegesException('Not enough privileges to revoke the role.');

    if ($this->exists($role))
      unset($this->meta[static::NAME][$role->getName()]);
  }


  /**
   * @brief Returns `true` if the role is already present, `false` otherwise.
   * @param[in] IRole $role A role object.
   * @retval bool
   */
  public function exists(IRole $role) {
    return isset($this->meta[static::NAME][$role->getName()]);
  }


  /**
   * @brief Returns `true` if the specified role is an instance of subclass (or an instance of the same class) of one of
   * the roles in the collection, `false` otherwise.
   * @param[in] IRole $role A role object.
   * @param[in] bool $orEqual (optional) When `false` doesn't check if the role is an instance of the same class.
   */
  public function areSuperiorThan(IRole $role, $orEqual = TRUE) {
    $result = FALSE;

    $roles = $this->meta[static::NAME];

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
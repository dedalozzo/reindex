<?php
/**
 * @file RoleCollection.php
 * @brief This file contains the RoleCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Security\Role\IRole;


/**
 * @brief This class is used to represent a collection of roles.
 * @nosubgrouping
 */
class RoleCollection extends AbstractCollection {

  const NAME = "roles";


  /**
   * @brief Grants the specified role to the current member.
   * @details The algorithm discards any role when a more important one has been granted to the member. That means you
   * can't add the Moderator role to an Admin, etc. You can also grant multiple roles to a member, to assign special
   * permissions.
   * @param[in] IRole $role A role object.
   */
  public function grant(IRole $role) {
    // Checks if the same role has been already assigned to the member.
    if ($this->exists($role->getName()))
      throw new \RuntimeException('The role has been already assigned to the member.');

    $roles = $this->meta[static::NAME];
    foreach ($roles as $name => $class) {
      // If a subclass of `$role` exists for the current collection then throw an exception, because a more important role
      // has been already assigned to the member.
      if (is_subclass_of($class, $role))
        throw new \RuntimeException('A more important role has been already assigned to the member.');

      // If `$role` is an instance of a subclass of any role previously assigned to the member that means the new role
      // is more important and the one already assigned must be removed.
      elseif (is_subclass_of($role, $class))
        unset($this->meta[static::NAME][$class]);
    }

    // Uses as key the role's name and as value its class.
    $this->meta[static::NAME][$role->getName()] = get_class($role);
  }


  /**
   * @brief Revokes the specified role to the current member.
   * @param[in] string $roleName A role's name.
   */
  public function revoke($roleName) {
    $this->remove($roleName);
  }


  /**
   * @brief Returns `true` if the role is already present, `false` otherwise.
   * @param[in] string $roleName A role's name.
   * @retval bool
   */
  public function exists($roleName) {
    return parent::exists($roleName);
  }


  public function getRoleStrategy(IRole $role) {
    // Ritorna la role strategy associata alla role passata come parametro oppure, in caso esista una subclasse della
    // role, tra le role associate all'utente, torna la role strategy associata a quella subclasse.
  }

}
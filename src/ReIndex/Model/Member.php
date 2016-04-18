<?php

/**
 * @file Member.php
 * @brief This file contains the Member class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Model;


use EoC\Opt\ViewQueryOpts;

use ReIndex\Extension;
use ReIndex\Collection;
use ReIndex\Exception;
use ReIndex\Helper\ClassHelper;
use ReIndex\Security\User\IUser;
use ReIndex\Security\Role\Permission;
use ReIndex\Security\Role\Permission\IPermission;
use ReIndex\Security\Role\MemberRole;


/**
 * @brief This class is used to represent a registered user.
 * @nosubgrouping
 */
class Member extends Storable implements IUser, Extension\ICount {
  use Extension\TCount;

  private $emails;    // Collection of e-mails.
  private $logins;    // Collection of consumers' logins.
  private $roles;     // Collection of roles.
  private $friends;   // List of friends.
  private $blacklist; // Blacklist.


  public function __construct() {
    parent::__construct();

    $this->meta['emails'] = [];
    $this->emails = new Collection\EmailCollection($this->meta);

    $this->meta['logins'] = [];
    $this->logins = new Collection\LoginCollection($this->meta);

    $this->meta['roles'] = [];
    $this->roles = new Collection\RoleCollection($this->meta);

    $this->meta['friends'] = [];
    //$this->roles = new Collection\FriendCollection($this->meta);

    $this->meta['blacklist'] = [];
    //$this->roles = new Collection\Blacklist($this->meta);
  }


  /**
   * @brief Given a e-mail, returns the gravatar URL for the corresponding user.
   * @param[in] string $email The user e-mail.
   * @retval string
   */
  public static function getGravatar($email) {
    return 'http://gravatar.com/avatar/'.md5(mb_strtolower($email, 'utf-8')).'?d=identicon';
  }


  /**
   * @brief Returns the user's favorite tags if any.
   * @retval array
   */
  public function getFavoriteTags() {
    $opts = new ViewQueryOpts();
    $opts->setKey($this->getId())->doNotReduce();
    $favorites = $this->couch->queryView("favorites", "byMemberTags", NULL, $opts);

    if ($favorites->isEmpty())
      return [];

    $opts->reset();
    $opts->doNotReduce();
    return $this->couch->queryView("tags", "allNames", array_column($favorites->asArray(), 'value'), $opts)->asArray();
  }


  /**
   * @brief Returns the actual user's age based on his birthday, `null`in case a the user's birthday is not available.
   * @retval int|null
   */
  public function getAge() {
    if ($this->issetBirthday()) {
      $now = new \DateTime();
      $birthdayTimestamp = $this->getBirthday();
      $birthday = new \DateTime("@$birthdayTimestamp");
      return $now->diff($birthday)->y;
    }
    else
      return NULL;
  }


  /**
   * @brief Last time the user has logged in.
   * @retval string The time expressed as `3 Aprile, 2013` or an empty string.
   */
  public function getLastVisit() {
    if (isset($this->meta['lastVisit']))
      return strftime('%e %B, %Y', $this->meta['lastVisit']);
    else
      return "";
  }


  /**
   * @brief Returns the elapsed time since the user registration.
   * @retval string
   */
  public function getElapsedTimeSinceRegistration() {
    return strftime('%e %B, %Y', $this->createdAt);
  }


  /**
   * @brief Returns the user's reputation.
   * @retval integer
   */
  public function getReputation() {
    $opts = new ViewQueryOpts();
    $opts->setKey([$this->id]);

    $reputation = $this->couch->queryView("reputation", "perUser", NULL, $opts)->getReducedValue();

    if ($reputation > 1)
      return $reputation;
    else
      return 1;
  }


  /**
   * @copydoc Storable::save()
   */
  public function save() {
    // We must grant at least the MemberRole for the current member.
    if (!$this->roles->isSuperior(new MemberRole()))
      $this->roles->grant(new MemberRole());

    parent::save();
  }

  
  /** @name Access Control Management Methods */
  //!@{

  /**
   * @copydoc IUser::has()
   */
  public function has(IPermission $permission) {
    $result = FALSE;

    // Gets the class name of the provided instance, pruned by its namespace.
    $className = ClassHelper::getClassName(get_class($permission));

    foreach ($this->roles as $roleName => $roleClass) {

      do {
        // Creates a reflection class for the roleName.
        $reflection = new \ReflectionClass($roleClass);

        // Gets the namespace for the roleName pruned of the class name.
        $namespaceName = $reflection->getNamespaceName();

        // Determines the permission class related to the roleName.
        $newPermissionClass = $namespaceName . '\\Permission\\' . $roleName . '\\' . $className;

        if (class_exists($newPermissionClass)) { // If a permission exists for the roleName...
          // Casts the original permission object to an instance of the determined class.
          $obj = $permission->castAs($newPermissionClass);

          // Invokes on it the check() method.
          $result = $obj->check();

          // Exits from the do while and foreach as well.
          break 2;
        }
        else { // Go back to the previous role class in the hierarchy. For example, from AdminRole to ModeratorRole.
          $roleClass = $reflection->getParentClass();

          if (!$roleClass) // No more roles in the hierarchy.
            break;
        }
      } while (TRUE);

    }

    return $result;
  }


  /**
   * @brief Impersonates the given user.
   * @param[in] IUser $user An anonymous user or a member instance.
   */
  public function impersonate(IUser $user) {
    if ($this->user->has(new Permission\Admin\ImpersonateMemberPermission($user)))
      $this->user = $user;
    else
      throw new Exception\NotEnoughPrivilegesException('Non hai sufficienti privilegi per impersonare un altro utente.');
  }


  /**
   * @brief This implementation returns always `false`.
   * @retval bool
   */
  public function isGuest() {
    return FALSE;
  }


  /**
   * @brief This implementation returns always `true`.
   * @retval bool
   */
  public function isMember() {
    return TRUE;
  }

  //!@}


  /** @name Ban Management Methods */
  //!@{

  /**
   * @brief Returns `true` if the ban is expired, otherwise `false`.
   * @retval bool
   */
  protected function isBanExpired() {
    if ($this->isMetadataPresent('bannedFor') == 'ever') {
      return FALSE;
    }
    else {
      $expireOn = (new \DateTime())->setTimestamp($this->meta['bannedOn'])->add(sprintf('P%dD', $this->meta['bannedFor']))->getTimestamp();
      return (time() > $expireOn) ? TRUE : FALSE;
    }
  }


  /**
   * @brief Bans the user.
   * @param[in] integer $days The ban duration in days. When zero, the ban is permanent.
   */
  public function ban($days = 0) {
    if (!$this->canBeBanned()) throw new Exception\NotEnoughPrivilegesException("Privilegi di accesso insufficienti.");

    $this->meta['banned'] = TRUE;
    $this->meta['bannedOn'] = time();

    if ($days)
      $this->meta['bannedFor'] = $days;
    else
      $this->meta['bannedFor'] = 'ever';

    $this->save();
  }


  /**
   * @brief Removes the ban.
   * @param[in] bool $ignore When `true` ignores the security check.
   */
  public function unban($ignore = FALSE) {
    if (!$this->canBeUnBanned() && !$ignore) throw new Exception\NotEnoughPrivilegesException("Privilegi di accesso insufficienti.");

    if ($this->isMetadataPresent('banned')) {
      unset($this->meta['banned']);
      unset($this->meta['bannedOn']);
      unset($this->meta['bannedFor']);
    }

    $this->save();
  }


  /**
   * @brief Returns `true` if the user has been banned.
   * @details When expired, removes the ban.
   * @retval bool
   */
  public function isBanned() {
    if ($this->isMetadataPresent('banned')) {

      if ($this->isBanExpired()) {
        $this->unban(TRUE);
        return FALSE;
      }
      else // It's a permanent ban.
        return TRUE;

    }
    else
      return FALSE;
  }

  //!@}


  //! @cond HIDDEN_SYMBOLS

  public function getFirstName() {
    return $this->meta['firstName'];
  }


  public function issetFirstName() {
    return isset($this->meta['firstName']);
  }


  public function setFirstName($value) {
    $this->meta['firstName'] = $value;
  }


  public function unsetFirstName() {
    if ($this->isMetadataPresent('firstName'))
      unset($this->meta['firstName']);
  }


  public function getLastName() {
    return $this->meta['lastName'];
  }


  public function issetLastName() {
    return isset($this->meta['lastName']);
  }


  public function setLastName($value) {
    $this->meta['lastName'] = $value;
  }


  public function unsetLastName() {
    if ($this->isMetadataPresent('lastName'))
      unset($this->meta['lastName']);
  }


  public function getUsername() {
    return $this->meta['username'];
  }


  public function issetUsername() {
    return isset($this->meta['username']);
  }


  public function setUsername($value) {
    $this->meta['username'] = $value;
  }


  public function unsetUsername() {
    if ($this->isMetadataPresent('username'))
      unset($this->meta['username']);
  }


  public function getEmails() {
    return $this->emails;
  }


  public function issetEmails() {
    return isset($this->emails);
  }


  public function getLogins() {
    return $this->logins;
  }


  public function issetLogins() {
    return isset($this->logins);
  }


  public function getRoles() {
    return $this->roles;
  }


  public function issetRoles() {
    return isset($this->roles);
  }


  public function getPassword() {
    return $this->meta['password'];
  }


  public function issetPassword() {
    return isset($this->meta['password']);
  }


  public function setPassword($value) {
    $this->meta['password'] = $value;
  }


  public function unsetPassword() {
    if ($this->isMetadataPresent('password'))
      unset($this->meta['password']);
  }


  public function getGender() {
    return $this->meta['gender'];
  }


  public function issetGender() {
    return isset($this->meta['gender']);
  }


  public function setGender($value) {
    $this->meta['gender'] = $value;
  }


  public function unsetGender() {
    if ($this->isMetadataPresent('gender'))
      unset($this->meta['gender']);
  }


  public function getBirthday() {
    return $this->meta['birthday'];
  }


  public function issetBirthday() {
    return isset($this->meta['birthday']);
  }


  public function setBirthday($value) {
    $this->meta['birthday'] = $value;
  }


  public function unsetBirthday() {
    if ($this->isMetadataPresent('birthday'))
      unset($this->meta['birthday']);
  }


  public function getAbout() {
    return $this->meta['about'];
  }


  public function issetAbout() {
    return isset($this->meta['about']);
  }


  public function setAbout($value) {
    $this->meta['about'] = $value;
  }

  
  public function unsetAbout() {
    if ($this->isMetadataPresent('about'))
      unset($this->meta['about']);
  }
  

  public function getInternetProtocolAddress() {
    return $this->meta['ipAddress'];
  }


  public function issetInternetProtocolAddress() {
    return isset($this->meta['ipAddress']);
  }


  public function setInternetProtocolAddress($value) {
    $this->meta['ipAddress'] = $value;
  }


  public function unsetInternetProtocolAddress() {
    if ($this->isMetadataPresent('ipAddress'))
      unset($this->meta['ipAddress']);
  }


  public function getHash() {
    return $this->meta['hash'];
  }


  public function issetHash() {
    return isset($this->meta['hash']);
  }


  public function setHash($value) {
    $this->meta['hash'] = $value;
  }


  public function unsetHash() {
    if ($this->isMetadataPresent('hash'))
      unset($this->meta['hash']);
  }

  
  public function getLocale() {
    return $this->meta['locale'];
  }


  public function issetLocale() {
    return isset($this->meta['locale']);
  }


  public function setLocale($value) {
    $this->meta['locale'] = $value;
  }


  public function unsetLocale() {
    if ($this->isMetadataPresent('locale'))
      unset($this->meta['locale']);
  }


  public function getTimeOffset() {
    return $this->meta['timeOffset'];
  }


  public function issetTimeOffset() {
    return isset($this->meta['timeOffset']);
  }


  public function setTimeOffset($value) {
    $this->meta['timeOffset'] = $value;
  }


  public function unsetTimeOffset() {
    if ($this->isMetadataPresent('timeOffset'))
      unset($this->meta['timeOffset']);
  }

  //! @endcond

}
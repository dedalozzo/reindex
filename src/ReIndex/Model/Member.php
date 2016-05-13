<?php

/**
 * @file Member.php
 * @brief This file contains the Member class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Model;


use ReIndex\Extension;
use ReIndex\Collection;
use ReIndex\Exception;
use ReIndex\Helper;
use ReIndex\Security\User\IUser;
use ReIndex\Security\Role;
use ReIndex\Security\Role\IPermission;
use ReIndex\Security\Role\MemberRole;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use Phalcon\Di;


/**
 * @brief This class is used to represent a registered user.
 * @nosubgrouping
 */
class Member extends Storable implements IUser, Extension\ICache, Extension\ICount {
  use Extension\TCount;

  const MR_HASH = '_mr'; //!< Members Redis hash.

  private $emails;    // Collection of e-mails.
  private $logins;    // Collection of consumers' logins.
  private $roles;     // Collection of roles.
  private $friends;   // List of friends.
  private $blacklist; // Blacklist.


  public function __construct() {
    parent::__construct();

    // Since we can't use reflection inside EoC Server, we need a way to recognize if a class implements the `ICache`
    // interface. This is done using a property `useCache`, we can test using `isset($doc->useCache)`.
    $this->meta['useCache'] = TRUE;

    $this->meta['emails'] = [];
    $this->emails = new Collection\EmailCollection($this->meta);

    $this->meta['logins'] = [];
    $this->logins = new Collection\LoginCollection($this->meta);

    $this->meta['roles'] = [];
    $this->roles = new Collection\RoleCollection($this->meta);

    // Friendships are stored outside the member scope.
    $this->friends = new Collection\FriendCollection($this);

    $this->meta['blacklist'] = [];
    $this->blacklist = new Collection\Blacklist($this->meta);
  }


  /**
   * @brief Returns a full name suitable for string comparison.
   * @retval string
   */
  protected function getFullNameForIndex() {
    $fullName = $this->firstName . $this->lastName;
    return empty($fullName) ? $this->username : strtolower($this->firstName . $this->lastName);
  }


  /**
   * @brief Returns an inverted full name suitable for string comparison.
   * @retval string
   */
  protected function getInvFullNameForIndex() {
    $fullName = $this->firstName . $this->lastName;
    return empty($fullName) ? $this->username : strtolower($this->lastName . $this->firstName);
  }


  /**
   * @brief Given a list of IDs, returns the correspondent objects.
   * @retval array
   */
  public static function collect(array $ids) {
    if (empty($ids))
      return [];

    $di = Di::getDefault();
    $couch = $di['couchdb'];
    $redis = $di['redis'];
    $user = $di['guardian']->getUser();

    $opts = new ViewQueryOpts();

    // Gets the members' properties.
    $opts->doNotReduce();
    $result = $couch->queryView("members", "all", $ids, $opts);

    // Retrieves the members reputation.
    //$opts->reset();
    //$opts->groupResults()->includeMissingKeys();
    //$reputations = $this->couch->queryView("reputation", "perMember", $keys, $opts);

    $members = [];
    $membersCount = count($result);
    for ($i = 0; $i < $membersCount; $i++) {
      $id = $result[$i]['id'];

      $member = new \stdClass();
      $member->id = $id;
      $member->username = $result[$i]['value'][0];
      $member->gravatar = Member::getGravatar($result[$i]['value'][1]);
      $member->createdAt = $result[$i]['value'][2];
      $member->fullName = $result[$i]['value'][3] . ' ' . $result[$i]['value'][4];
      $member->headline = $result[$i]['value'][5];
      $member->when = Helper\Time::when($member->createdAt, false);

      // Friendship.
      $opts->reset();
      $opts->doNotreduce()->setLimit(1)->setKey([$user->id, $id]);
      $member->friendshipExists = !$couch->queryView("friendships", "approvedPerMember", NULL, $opts)->isEmpty();

      // Hits count.
      $member->hitsCount = Helper\Text::formatNumber($redis->hGet($id, 'hits'));

      // Friends count.
      $opts->reset();
      $opts->reduce()->reverseOrderOfResults()->setStartKey([$id, Couch::WildCard()])->setEndKey([$id]);
      $member->friendsCount = Helper\Text::formatNumber($couch->queryView("friendships", "approvedPerMember", NULL, $opts)->getReducedValue());

      // Followers count.
      $opts->reset();
      $opts->reduce()->setKey($id);
      $member->followersCount = Helper\Text::formatNumber($couch->queryView("followers", "perMember", NULL, $opts)->getReducedValue());

      $members[] = $member;
    }

    return $members;
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
   * @brief Saves the member.
   * @param[in] bool $deferred When `true` doesn't update the indexes.
   */
  public function save($deferred = FALSE) {
    $memberRole = new MemberRole();

    // We must grant at least the MemberRole for the current member.
    if (!$this->roles->areSuperiorThan($memberRole))
      $this->roles->grant($memberRole);

    parent::save();

    if (!$deferred) {
      $this->index();
    }
  }


  /** @name Indexing Methods */
  //!@{

  /**
   * @copydoc ICache::index()
   */
  public function index() {
    $key = $this->id . self::MR_HASH;

    // Returns the values associated with the specified fields in the hash stored at member ID.
    // For every field that does not exist in the hash, a nil value is returned. Because a non-existing keys are treated
    // as empty hashes, running `hMGet()` against a non-existing key will return a list of `null` values.
    $hash = $this->redis->hMGet($key, ['username', 'fullName', 'invFullName']);

    $username = $this->username;
    $fullName = $this->getFullNameForIndex();
    $invFullName = $this->getInvFullNameForIndex();

    // Username or full name has been changed or the member has never been indexed.
    if ($hash['username'] != $username or $hash['fullName'] != $fullName) {
      $opts = new ViewQueryOpts();
      $opts->doNotReduce()->setKey([$this->user->id]);
      $rows = $this->couch->queryView("friendship", "approvedPerMember", NULL, $opts);

      $this->redis->multi();

      foreach ($rows as $row) {
        $memberId = $row['id'][1];
        $friendId = $this->id;

        $this->redis->zRem($memberId . Collection\FriendCollection::TS_SET, $friendId);
        $this->redis->zRem($memberId . Collection\FriendCollection::UN_SET, $hash['username'].':'.$friendId);
        $this->redis->zRem($memberId . Collection\FriendCollection::FN_SET, $hash['fullName'].':'.$friendId);
        $this->redis->zRem($memberId . Collection\FriendCollection::IFN_SET, $hash['invFullName'].':'.$friendId);

        $this->redis->zAdd($memberId . Collection\FriendCollection::TS_SET, $row['value'], $friendId);
        $this->redis->zAdd($memberId . Collection\FriendCollection::UN_SET, 0, $hash['username'].':'.$friendId);
        $this->redis->zAdd($memberId . Collection\FriendCollection::FN_SET, 0, $hash['fullName'].':'.$friendId);
        $this->redis->zAdd($memberId . Collection\FriendCollection::IFN_SET, 0, $hash['invFullName'].':'.$friendId);
      }

      $this->redis->hMSet($this->id . self::MR_HASH, ['username' => $username, 'fullName' => $fullName, 'invFullName' => $invFullName]);

      $this->redis->exec();
    }
  }

  //!@}


  /** @name Access Control Management Methods */
  //!@{

  /**
   * @copydoc IUser::has()
   */
  public function has(IPermission $permission) {
    $result = FALSE;

    $permissionReflection = new \ReflectionObject($permission);

    // Gets the class name of the provided instance, pruned by its namespace.
    $permissionClassName = $permissionReflection->getShortName();

    // Determines the namespace excluded the role name.
    $root = Helper\ClassHelper::getClassRoot($permissionReflection->getNamespaceName());

    foreach ($this->roles as $roleName => $roleClass) {

      do {
        $role = new $roleClass;

        // Creates a reflection class for the roleName.
        $roleReflection = new \ReflectionObject($role);

        // Determines the permission class related to the roleName.
        $newPermissionClass = $root . $roleReflection->getShortName() . '\\' . $permissionClassName;

        if (class_exists($newPermissionClass)) { // If a permission exists for the roleName...
          // Sets the execution role for the current user.
          $permission->setRole($role);

          // Casts the original permission object to an instance of the determined class.
          $obj = $permission->castAs($newPermissionClass);

          // Invokes on it the check() method.
          $result = $obj->check();

          // Exits from the do while and foreach as well.
          break 2;
        }
        else { // Go back to the previous role class in the hierarchy. For example, from AdminRole to ModeratorRole.
          $parentRoleReflection = $roleReflection->getParentClass();

          // Proceed only if the parent role is not an abstract class.
          if (is_object($parentRoleReflection) && !$parentRoleReflection->isAbstract())
            $roleClass = $parentRoleReflection->name;
          else
            break; // No more roles in the hierarchy.
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
    if ($this->user->has(new Role\AdminRole\ImpersonatePermission($user)))
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
    if (!$this->user->has(new Role\ModeratorRole\BanMemberPermission($this)))
      throw new Exception\NotEnoughPrivilegesException("Privilegi di accesso insufficienti.");

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
   */
  public function unban() {
    if (!$this->user->has(new Role\ModeratorRole\UnbanMemberPermission($this)))
      throw new Exception\NotEnoughPrivilegesException("Privilegi di accesso insufficienti.");

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


  public function getFriends() {
    return $this->friends;
  }


  public function issetFriends() {
    return isset($this->friends);
  }


  public function getFollowers() {
    return $this->followers;
  }


  public function issetFollowers() {
    return isset($this->followers);
  }


  public function getBlacklist() {
    return $this->blacklist;
  }


  public function issetBlacklist() {
    return isset($this->blacklist);
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
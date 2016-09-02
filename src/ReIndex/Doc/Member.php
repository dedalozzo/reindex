<?php

/**
 * @file Member.php
 * @brief This file contains the Member class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use ReIndex\Collection;
use ReIndex\Exception;
use ReIndex\Helper;
use ReIndex\Security\User\IUser;
use ReIndex\Security\Role;
use ReIndex\Security\Permission\IPermission;
use ReIndex\Security\Role\MemberRole;
use ReIndex\Task\IndexMemberTask;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use Phalcon\Di;


/**
 * @brief This class is used to represent a registered user.
 * @nosubgrouping
 * 
 * @cond HIDDEN_SYMBOLS
 *
 * @property string $username
 * @property string $firstName
 * @property string $lastName

 * @property Collection\TaskCollection $tasks
 * @property Collection\EmailCollection $emails
 * @property Collection\LoginCollection $logins
 * @property Collection\RoleCollection $roles
 * @property Collection\TagCollection $tags
 * @property Collection\Blacklist $blacklist
 * @property Collection\FriendCollection $friends
 * @property Collection\FollowerCollection $followers

 * @property string $password
 * @property string $hash
 * @property string $internetProtocolAddress
 * @property string $locale
 * @property int $timeOffset

 * @property string $gender
 * @property int $birthday
 * @property string $about
 *
 * @endcond
 */
final class Member extends ActiveDoc implements IUser {

  /** @name Constants */
  //!@{

  const MR_HASH = '_mr'; //!< Member's Redis hash postfix.
  const HP_SET = 'hp_'; //!< Member's homepage Redis set prefix.
  const TL_SET = 'tl_'; //!< Member's timeline Redis set prefix.

  //!@}

  private $tasks;     // Collection of tasks.
  private $emails;    // Collection of e-mails.
  private $logins;    // Collection of consumers' logins.
  private $roles;     // Collection of roles.
  private $tags;      // Favorite tags.
  private $blacklist; // Blacklist.
  private $friends;   // List of friends.
  private $followers; // List of followers.


  public function __construct() {
    parent::__construct();

    $this->tasks = new Collection\TaskCollection('tasks', $this->meta);
    $this->emails = new Collection\EmailCollection('emails', $this->meta);
    $this->logins = new Collection\LoginCollection('logins', $this->meta);
    $this->roles = new Collection\RoleCollection('roles', $this->meta);
    $this->tags = new Collection\TagCollection('tags', $this->meta);
    $this->blacklist = new Collection\Blacklist('blacklist', $this->meta);
    $this->friends = new Collection\FriendCollection($this);
    $this->followers = new Collection\FollowerCollection($this);
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
    //$redis = $di['redis'];
    $user = $di['guardian']->getUser();

    $opts = new ViewQueryOpts();

    // Gets the members' properties.
    $opts->doNotReduce();
    // members/info/view
    $result = $couch->queryView('members', 'info', 'view', $ids, $opts);

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
      if ($user->isMember()) {
        $opts->reset();
        // `true` means: approved friendship
        $opts->doNotReduce()->setLimit(1)->setKey([TRUE, $user->id, $id]);
        // friendships/relations/view
        $member->friendshipExists = !$couch->queryView('friendships', 'relations', 'view', NULL, $opts)->isEmpty();
      }
      else
        $member->friendshipExists = FALSE;

      // Friends count.
      $opts->reset();
      $opts->reduce();
      $opts->setStartKey([TRUE, $id, Couch::WildCard()])->setEndKey([TRUE, $id])->reverseOrderOfResults();
      // friendships/relations/view
      $member->friendsCount = Helper\Text::formatNumber($couch->queryView('friendships', 'relations', 'view', NULL, $opts)->getReducedValue());

      // Followers count.
      $opts->reset();
      $opts->reduce()->setKey($id);
      // followers/perMember/view
      $member->followersCount = Helper\Text::formatNumber($couch->queryView('followers', 'perMember', 'view', NULL, $opts)->getReducedValue());

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
   * @copydoc ActiveDoc::getDbName()
   */
  protected function getDbName() {
    return 'members';
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
   * @brief @copydoc ActiveDoc::save()
   */
  public function save($update = TRUE) {
    $memberRole = new MemberRole();

    // We must grant at least the MemberRole for the current member.
    if (!$this->roles->areSuperiorThan($memberRole))
      $this->roles->grant($memberRole);

    $this->tasks->add(new IndexMemberTask($this));

    parent::save($update);
  }


  /** @name Access Control Management Methods */
  //!@{

  /**
   * @copydoc IUser::has()
   */
  public function has(IPermission $permission) {
    $result = FALSE;

    $permissionReflection = new \ReflectionObject($permission);

    foreach ($this->roles as $roleName => $roleClass) {

      do {
        $role = new $roleClass;

        // Sets the execution role for the current user.
        $permission->setRole($role);

        // Creates a reflection class for the roleName.
        $roleReflection = new \ReflectionObject($role);

        // Determines the method's name related to the roleName.
        $methodName = 'checkFor' . $roleReflection->getShortName();

        if ($permissionReflection->hasMethod($methodName)) { // If a method exists for the roleName...
          // Gets the method.
          $method = $permissionReflection->getMethod($methodName);

          // Invokes the method.
          $result = $method->invoke($permission);

          // Exits from the do while and foreach as well.
          break 2;
        }
        else {
          // Go back to the previous role class in the hierarchy. For example, from AdminRole to ModeratorRole.
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
    if ($this->user->has(new Role\Permission\Member\ImpersonatePermission($user)))
      $this->user = $user;
    else
      throw new Exception\AccessDeniedException('Non hai sufficienti privilegi per impersonare un altro utente.');
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
    if (!$this->user->has(new Role\Permission\Member\BanPermission($this)))
      throw new Exception\AccessDeniedException("Privilegi di accesso insufficienti.");

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
    if (!$this->user->has(new Role\Permission\Member\UnbanPermission($this)))
      throw new Exception\AccessDeniedException("Privilegi di accesso insufficienti.");

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
        $this->unban();
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

  public function getTags() {
    return $this->tags;
  }


  public function issetTags() {
    return isset($this->tags);
  }

  //! @endcond

}
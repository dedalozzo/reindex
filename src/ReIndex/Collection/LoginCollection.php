<?php
/**
 * @file LoginCollection.php
 * @brief This file contains the LoginCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


/**
 * @brief This class is used to represent a collection of consumers' logins.
 * @nosubgrouping
 */
class LoginCollection extends AbstractCollection {

  const NAME = "logins";


  /**
   * @brief Returns a login name based on the user id and consumer name.
   * @param[in] string $userId The user id.
   * @param[in] string $consumerName The consumer name.
   * @retval string
   */
  private function buildLoginName($userId, $consumerName) {
    return $userId.'@'.$consumerName;
  }


  /**
   * @brief Searches for the user identified by the specified email, if any returns it, otherwise return `false`.
   * @param[in] string $consumerName The consumer name.
   * @param[in] string $userId The user id.
   * @param[in] string $profileUrl The user's profile URL.
   * @param[in] string $email The user's e-mail.
   * @param[in] string $username The username.
   * @param[in] bool $verified The e-mail is verified or not.
   */
  public function add($consumerName, $userId, $profileUrl, $email, $username, $verified) {
    $login = $this->buildLoginName($userId, $consumerName);
    $this->meta[self::NAME][$login] = [$consumerName, (string)$userId, $email, $profileUrl, $username];

    // Adds the e-mail and eventually set its address as primary.
    $this->meta['emails'][$email] = $verified;

    if (count($this->meta['emails']) == 1)
      $this->meta['primaryEmail'] = $email;
  }


  /**
   * @brief Removes the specified provider and all its information.
   * @param[in] string $login The login address ($userId@$consumerName).
   * @attention The e-mail associated to the login is never removed from the list of e-mails.
   */
  public function remove($login) {
    parent::remove($login);
  }


  /**
   * @brief Returns `true` if the user login is already present, `false` otherwise.
   * @param[in] string $login The login address ($userId@$consumerName).
   * @retval bool
   */
  public function exists($login) {
    return parent::exists($login);
  }


}
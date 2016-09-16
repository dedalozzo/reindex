<?php

/**
 * @file Content.php
 * @brief This file contains the Content class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use EoC\Opt\ViewQueryOpts;

use ReIndex\Collection;
use ReIndex\Property\TBody;


/**
 * @brief A content created by a user.
 * @nosubgrouping
 *
 * @cond HIDDEN_SYMBOLS
 *
 * @property string $body
 * @property string $html
 *
 * @property string $creatorId
 *
 * @property Collection\VoteCollection $votes
 *
 * @endcond
 */
abstract class Content extends ActiveDoc {
  use TBody;

  private $username = NULL;
  private $gravatar = NULL;
  private $votes;

  /**
   * @var Hoedown $markdown
   */
  protected $markdown;


  /**
   * @brief Constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->markdown = $this->di['markdown'];
    $this->votes = new Collection\VoteCollection($this);
  }


  /**
   * @brief Parses the body.
   */
  public function parseBody() {
    if (is_null($this->body))
      return;

    $this->html = $this->markdown->parse($this->body);
  }


  /**
   * @copydoc ActiveDoc::save()
   */
  public function save($update = TRUE) {
    $userId = $this->user->getId();

    // Creator ID has not been provided.
    if (!isset($this->creatorId) && isset($userId))
      $this->creatorId = $userId;

    parent::save($update);
  }



  /**
   * @brief Using the lady loading pattern, this method retrieves a couple of author's properties.
   * @details Since the members data resides on a database, the system prevent from loading them, unless they are
   * strictly needed.
   */
  private function retrieveAuthorInfo() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setKey($this->creatorId);
    // members/names/view
    $row = $this->couch->queryView('members', 'names', 'view', NULL, $opts)[0]['value'];
    $this->username = $row[0];
    $this->gravatar = 'http://gravatar.com/avatar/'.md5(strtolower($row[1])).'?d=identicon';
  }


  /**
   * @brief Returns the author's username.
   * @retval string
   */
  public function getAuthorUsername() {
    if (is_null($this->username))
      $this->retrieveAuthorInfo();

    return $this->username;
  }


  /**
   * @brief Returns the gravatar uri.
   * @retval string
   */
  public function getAuthorGravatar() {
    if (is_null($this->username))
      $this->retrieveAuthorInfo();

    return $this->gravatar;
  }


  //! @cond HIDDEN_SYMBOLS

  public function getCreatorId() {
    return $this->meta["creatorId"];
  }


  public function issetCreatorId() {
    return isset($this->meta['creatorId']);
  }


  public function setCreatorId($value) {
    $this->meta["creatorId"] = $value;
  }


  public function unsetCreatorId() {
    if ($this->isMetadataPresent('creatorId'))
      unset($this->meta['creatorId']);
  }


  public function getVotes() {
    return $this->votes;
  }


  public function issetVotes() {
    return isset($this->votes);
  }

  //! @endcond

}
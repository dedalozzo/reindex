<?php

//! @file Post.php
//! @brief This file contains the Post class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model;


use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;
use ElephantOnCouch\Helper\TimeHelper;

use PitPress\Model\User\User;


//! @brief This class is used to represent a generic entry, a content created by a user.
//! @details Every post is versioned into the database, has tags and also a owner, who created the entry.
//! @nosubgrouping
abstract class Post extends VersionedItem {


  //! @brief Constructor.
  public function __construct() {
    parent::__construct();
    $this->meta['supertype'] = 'post';
    $this->meta['section'] = $this->getSection();
  }


  private function vote(User $currentUser, $choice) {
    $voted = $this->didUserVote($currentUser, $voteId);

    if ($voted) {
      // Gets the vote.
      $doc = $this->couch->getDoc(Couch::STD_DOC_PATH, $voteId);

      // Calculates difference in seconds.
      $seconds = floor(time() / $doc->getTimestamp());

      // The user has 5 minutes to change his vote.
      if ($seconds < 300) {
        $doc->setChoice($choice);
        $this->couch-saveDoc($doc);
      }
      else
        throw new \RuntimeException("Trascorsi 5 minuti non è più possibile rettificare il proprio voto.");
    }
    else {
      $doc = Accessory\Vote::create($this->postType, $this->postSection, $this->id, $currentUser->id, $choice);
      $this->couch->saveDoc($doc);
    }
  }


  //! @brief Gets the item permanent link.
  //! @return string
  public function getPermalink() {
    return "/".$this->getSection()."/".$this->id;
  }


  //! @brief The post belongs to this section.
  //! @return string
  abstract public function getSection();


  //! @brief Gets the post type in a human readable form.
  //! @return string
  abstract public function getHumanReadableType();


  //! @brief Returns the publishing date in a human readable format.
  //! @return string
  public function getHumanReadableDate() {
    $publishingDate = $this->getPublishingDate();

    $today = date('Ymd');

    // Today.
    if ($today == date('Ymd', $publishingDate)) {
      $time = TimeHelper::since($publishingDate);

      if ($time['hours'] == 1)
        return "un'ora fa";
      elseif ($time['hours'] > 1)
        return sprintf('$d ore fa', $time['hours']);
      elseif ($time['minutes'] == 1)
        return "un minuto fa";
      elseif ($time['minutes'] > 1)
        return sprintf('$d minuti fa', $time['minutes']);
      elseif ($time['seconds'] == 1)
        return "un secondo fa";
      elseif ($time['seconds'] > 1)
        return sprintf('$d secondi fa', $time['seconds']);
    }
    // Yesterday.
    elseif (strtotime('-1 day', $today) == date('Ymd', $publishingDate)) {
      return "ieri";
    }
    // In the past.
    else {
      return date('d-m-Y \a\l\l\e H:i', $publishingDate);
    }
  }


  //! @brief Gets the number of the answer or comments.
  public function getReplaysCount() {

  }


  //! @brief Gets the associated tags list.
  public function getTags() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setKey($this->id);

    $result = $this->couch->queryView("classifications", "perPost", NULL, $opts)->getBodyAsArray();

    $keys = [];
    foreach ($result['rows'] as $classification)
      $keys[] = $classification['value'];

    $opts->reset();
    $opts->doNotReduce();

    return $this->couch->queryView("tags", "all", $keys, $opts)->getBodyAsArray();
  }


  //! @name Tagging Methods
  // @{

  //! @brief Removes all tags.
  public function resetTags() {

  }


  //! @brief Adds the specified tag to the tags list.
  public function addTag() {

  }


  //! @brief Adds many tags at once to the tags list.
  public function addMultipleTagsAtOnce() {

  }

  //@}


  //! @name Voting Methods
  // @{

  //! @brief Likes an post.
  //! @param[in] User $currentUser The current user logged in.
  public function voteUp(User $currentUser) {
    $this->vote($currentUser, '+');
  }


  //! @brief Unlikes a post.
  //! @param[in] User $currentUser The current user logged in.
  public function voteDown(User $currentUser) {
    $this->vote($currentUser, '-');
  }


  //! @brief Returns <i>true</i> if the user has voted else otherwise.
  //! @param[in] User $currentUser The current user logged in.
  //! @return boolean
  public function didUserVote(User $currentUser, &$voteId = NULL) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([$this->id, $currentUser->id]);

    $result = $this->couch->queryView("votes", "perPost", NULL, $opts)->getBodyAsArray();

    if (empty($result['rows']))
      return FALSE;
    else {
      $voteId = $result['rows'][0]['id'];
      return TRUE;
    }
  }


  //! @brief Returns the arithmetic sum of each each vote.
  //! @return integer
  public function getVotesCount() {
    $opts = new ViewQueryOpts();
    $opts->setKey([$this->id]);

    $result = $this->couch->queryView("votes", "perPost", NULL, $opts)->getBodyAsArray();

    if (empty($result['rows']))
      return 0;
    else
      return $result['rows'][0]['value'];
  }


  //! @brief Returns the thumbs state expressed by the current user in relation to the current post.
  //! @param[in] User $currentUser The current user logged in.
  //! @return string|boolean Returns <i>false</i> in case the user never voted, '+' for thumbs up and '-' for thumbs down.
  public function getThumbsDirection(User $currentUser) {
    return $this->redis->hGet($currentUser->id, $this->id);
  }

  //@}


  //! @name Properties Accessors
  //@{

  public function getTitle() {
    return $this->meta['title'];
  }


  public function issetTitle() {
    return isset($this->meta['title']);
  }


  public function setTitle($value) {
    $this->meta['title'] = $value;
  }


  public function unsetTitle() {
    if ($this->isMetadataPresent('title'))
      unset($this->meta['title']);
  }

  //! @}

}
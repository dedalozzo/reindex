<?php

//! @file Mode
//! @brief This file contains the Item class.
//! @details
//! @author Filippo F. Fadda


//! @brief PitPress models namespace.
namespace PitPress\Model;


use PitPress\Model\Accessory\Star;
use PitPress\Model\Accessory\Subscription;
use PitPress\Model\User\User;

use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;


//! @brief This class represents an abstract versioned item.
//! @nosubgrouping
abstract class VersionedItem extends Item {

  //! @name Starring Methods
  //@{

  //! @brief Returns `true` if the current user starred this post.
  //! @param[in] User $currentUser The current user logged in.
  //! @param[out] string $starId The star document identifier related to the current post.
  //! @return boolean
  public function isStarred(User $currentUser, &$starId = NULL) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([$this->id, $currentUser->id]);

    $result = $this->couch->queryView("stars", "perItem", NULL, $opts)->getBodyAsArray();

    if (empty($result['rows']))
      return FALSE;
    else {
      $starId = $result['rows'][0]['id'];
      return TRUE;
    }
  }


  //! @brief Adds the item to the favourites list of the current user.
  //! @param[in] User $currentUser The current user logged in.
  public function star(User $currentUser) {
    if (!$this->isStarred($currentUser)) {
      $doc = Star::create($this->id, $currentUser->id);
      $this->couch->saveDoc($doc);
    }
  }


  //! @brief Removes the item from the favourites list of the current user.
  //! @param[in] User $currentUser The current user logged in.
  public function unstar(User $currentUser) {
    if ($this->isStarred($currentUser, $starId)) {
      $doc = $this->couch->getDoc(Couch::STD_DOC_PATH, $starId);
      $doc->delete();
      $this->couch->saveDoc($doc);
    }
  }


  //! @brief Returns the number of times the item has been starred.
  //! @return integer
  public function getStarsCount() {
    $opts = new ViewQueryOpts();
    $opts->setKey([$this->id]);

    $result = $this->couch->queryView("stars", "perItem", NULL, $opts)->getBodyAsArray();

    if (empty($result['rows']))
      return 0;
    else
      return $result['rows'][0]['value'];
  }

  //! @}


  //! @name Subscribing Methods
  // @{

  //! @brief Returns `true` if the user has subscribed the current post.
  //! @param[in] User $currentUser The current user logged in.
  //! @return boolean
  public function isSubscribed(User $currentUser, &$subscriptionId = NULL) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([$this->id, $currentUser->id]);

    $result = $this->couch->queryView("subscriptions", "perItem", NULL, $opts)->getBodyAsArray();

    if (empty($result['rows']))
      return FALSE;
    else {
      $subscriptionId = $result['rows'][0]['id'];
      return TRUE;
    }
  }


  //! @brief The current user will get notifications about changes related to the current item.
  //! @param[in] User $currentUser The current user logged in.
  public function subscribe(User $currentUser) {
    if (!$this->isSubscribed($currentUser)) {
      $doc = Subscription::create($this->id, $currentUser->id);
      $this->couch->saveDoc($doc);
    }
  }


  //! @brief The current user won't get notifications anymore.
  //! @param[in] User $currentUser The current user logged in.
  public function unsubscribe(User $currentUser) {
    if ($this->isSubscribed($currentUser, $subscriptionId)) {
      $doc = $this->couch->getDoc(Couch::STD_DOC_PATH, $subscriptionId);
      $doc->delete();
      $this->couch->saveDoc($doc);
    }
  }


  //! @brief Returns the number of users have been subscribed the item.
  //! @return integer
  public function getSubscribersCount() {
    $opts = new ViewQueryOpts();
    $opts->setKey([$this->id]);

    $result = $this->couch->queryView("subscriptions", "perItem", NULL, $opts)->getBodyAsArray();

    if (empty($result['rows']))
      return 0;
    else
      return $result['rows'][0]['value'];
  }

  //@}


  //! @name Control Versioning Methods
  //@{

  //! @brief Retrieves the list of changes.
  public function getChanges() {
  }


  //! @brief Rollbacks to the specified version.
  public function rollback($version) {
  }

  //! @}


  //! @cond HIDDEN_SYMBOLS

  public function getOwnerId() {
    return $this->meta["ownerId"];
  }


  public function issetOwnerId() {
    return isset($this->meta['ownerId']);
  }


  public function setOwnerId($value) {
    $this->meta["ownerId"] = $value;
  }

  
  public function unsetOwnerId() {
    if ($this->isMetadataPresent('ownerId'))
      unset($this->meta['ownerId']);
  }


  public function getCreator() {
    return $this->meta['creator'];
  }


  public function issetCreator() {
    return isset($this->meta['creator']);
  }


  public function setCreator($value) {
    $this->meta['creator'] = $value;
  }


  public function unsetCreator() {
    if ($this->isMetadataPresent('creator'))
      unset($this->meta['creator']);
  }


  public function getPublishingDate() {
    return $this->meta['publishingDate'];
  }


  public function issetPublishingDate() {
    return isset($this->meta['publishingDate']);
  }


  public function setPublishingDate($value) {
    $this->meta['publishingDate'] = $value;
  }


  public function unsetPublishingDate() {
    if ($this->isMetadataPresent('publishingDate'))
      unset($this->meta['publishingDate']);
  }

  //! @endcond

}
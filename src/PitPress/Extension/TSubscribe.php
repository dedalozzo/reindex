<?php

//! @file TSubscribe.php
//! @brief This file contains the TSubscribe class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Extension;


use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Model\Accessory\Subscription;
use PitPress\Model\User\User;


//! @brief Implements the ISubscribe interface.
trait TSubscribe {

  //! @copydoc ISubscribe
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


//! @copydoc ISubscribe
  public function subscribe(User $currentUser) {

    if (!$this->isSubscribed($currentUser)) {
      $doc = Subscription::create($this->id, $currentUser->id);
      $this->couch->saveDoc($doc);
    }
  }


  //! @copydoc ISubscribe
  public function unsubscribe(User $currentUser) {
    if ($this->isSubscribed($currentUser, $subscriptionId)) {
      $doc = $this->couch->getDoc(Couch::STD_DOC_PATH, $subscriptionId);
      $doc->delete();
      $this->couch->saveDoc($doc);
    }
  }


  //! @copydoc ISubscribe
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

}
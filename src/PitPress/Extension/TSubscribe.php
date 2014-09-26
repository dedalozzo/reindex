<?php

/**
 * @file TSubscribe.php
 * @brief This file contains the TSubscribe class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Extension;


use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Model\Accessory\Subscription;
use PitPress\Model\User;
use PitPress\Helper\Text;


/**
 * @brief Implements the ISubscribe interface.
 */
trait TSubscribe {


  public function isSubscribed(User $user, &$subscriptionId = NULL) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([Text::unversion($this->id), $user->id]);

    $result = $this->couch->queryView("subscriptions", "perItem", NULL, $opts);

    if ($result->isEmpty())
      return FALSE;
    else {
      $subscriptionId = $result[0]['id'];
      return TRUE;
    }
  }


  public function subscribe(User $user) {

    if (!$this->isSubscribed($user)) {
      $doc = Subscription::create(Text::unversion($this->id), $user->id);
      $this->couch->saveDoc($doc);
    }
  }


  public function unsubscribe(User $user) {
    if ($this->isSubscribed($user, $subscriptionId)) {
      $doc = $this->couch->getDoc(Couch::STD_DOC_PATH, $subscriptionId);
      $doc->delete();
      $this->couch->saveDoc($doc);
    }
  }


  public function getSubscribersCount() {
    $opts = new ViewQueryOpts();
    $opts->setKey([$this->getUnversionId()]);

    return $this->couch->queryView("subscriptions", "perItem", NULL, $opts)->getReducedValue();
  }

}
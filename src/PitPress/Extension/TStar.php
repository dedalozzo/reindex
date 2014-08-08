<?php

/**
 * @file TStar.php
 * @brief This file contains the TStar trait.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Extension;


use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Model\Accessory\Star;
use PitPress\Model\User\User;
use PitPress\Model\Item;


/**
 * @brief Implements the IStar interface.
 */
trait TStar {


  public function isStarred(User $user = NULL, &$starId = NULL) {
    // In case there is no user logged in returns false.
    if (is_null($user)) return FALSE;

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([$this->id, $user->id]);

    $result = $this->couch->queryView("stars", "perItem", NULL, $opts);

    if ($result->isEmpty())
      return FALSE;
    else {
      $starId = $result[0]['id'];
      return TRUE;
    }
  }


  public function star(User $user = NULL) {
    if (is_null($user)) return Item::NO_USER_LOGGED_IN;

    if ($this->isStarred($user, $starId)) {
      $star = $this->couch->getDoc(Couch::STD_DOC_PATH, $starId);
      $this->couch->deleteDoc(Couch::STD_DOC_PATH, $starId, $star->rev);
      return IStar::UNSTARRED;
    }
    else {
      $doc = Star::create($user->id, $this);
      $this->couch->saveDoc($doc);
      return IStar::STARRED;
    }
  }


  public function getStarsCount() {
    $opts = new ViewQueryOpts();
    $opts->setKey([$this->id]);

    return $this->couch->queryView("stars", "perItem", NULL, $opts)->getReducedValue();
  }

}
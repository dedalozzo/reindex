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


/**
 * @brief Implements the IStar interface.
 */
trait TStar {


  public function isStarred(User $user, &$starId = NULL) {
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


  public function star(User $user) {
    if (!$this->isStarred($user)) {
      $doc = Star::create($user->id, $this->id, $this->getType());
      $this->couch->saveDoc($doc);
    }
  }


  public function unstar(User $user) {
    if ($this->isStarred($user, $starId)) {
      $doc = $this->couch->getDoc(Couch::STD_DOC_PATH, $starId);
      $doc->delete();
      $this->couch->saveDoc($doc);
    }
  }


  public function getStarsCount() {
    $opts = new ViewQueryOpts();
    $opts->setKey([$this->id]);

    return $this->couch->queryView("stars", "perItem", NULL, $opts)->getReducedValue();;
  }

}
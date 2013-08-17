<?php

//! @file TStar.php
//! @brief This file contains the TStar trait.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Extension;


use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Model\Accessory\Star;
use PitPress\Model\User\User;


//! @brief Implements the IStar interface.
trait TStar {

  //! @copydoc IStar
  public function isStarred(User $user, &$starId = NULL) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([$this->id, $user->id]);

    $result = $this->couch->queryView("stars", "perItem", NULL, $opts)->getBodyAsArray();

    if (empty($result['rows']))
      return FALSE;
    else {
      $starId = $result['rows'][0]['id'];
      return TRUE;
    }
  }


  //! @copydoc IStar
  public function star(User $user) {
    if (!$this->isStarred($user)) {
      $doc = Star::create($this->id, $user->id);
      $this->couch->saveDoc($doc);
    }
  }


  //! @copydoc IStar
  public function unstar(User $user) {
    if ($this->isStarred($user, $starId)) {
      $doc = $this->couch->getDoc(Couch::STD_DOC_PATH, $starId);
      $doc->delete();
      $this->couch->saveDoc($doc);
    }
  }


  //! @copydoc IStar
  public function getStarsCount() {
    $opts = new ViewQueryOpts();
    $opts->setKey([$this->id]);

    $result = $this->couch->queryView("stars", "perItem", NULL, $opts)->getBodyAsArray();

    if (empty($result['rows']))
      return 0;
    else
      return $result['rows'][0]['value'];
  }

}
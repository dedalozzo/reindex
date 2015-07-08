<?php

/**
 * @file TStar.php
 * @brief This file contains the TStar trait.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Extension;


use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use PitPress\Exception;
use PitPress\Model\Star;
use PitPress\Helper\Text;


/**
 * @brief Implements the IStar interface.
 */
trait TStar {


  public function isStarred(&$starId = NULL) {
    // In case there is no user logged in, returns false.
    if ($this->user->isGuest()) return FALSE;

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([Text::unversion($this->id), $this->user->id]);

    $result = $this->couch->queryView("stars", "perItem", NULL, $opts);

    if ($result->isEmpty())
      return FALSE;
    else {
      $starId = $result[0]['id'];
      return TRUE;
    }
  }


  public function star() {
    if ($this->user->isGuest()) throw new Exception\NoUserLoggedInException('Nessun utente loggato nel sistema.');

    if ($this->isStarred($starId)) {
      $star = $this->couch->getDoc(Couch::STD_DOC_PATH, $starId);
      $this->couch->deleteDoc(Couch::STD_DOC_PATH, $starId, $star->rev);
      return IStar::UNSTARRED;
    }
    else {
      $doc = Star::create($this->user->id, $this);
      $this->couch->saveDoc($doc);
      return IStar::STARRED;
    }
  }


  public function getStarsCount() {
    $opts = new ViewQueryOpts();
    $opts->setKey([Text::unversion($this->id)]);

    return $this->couch->queryView("stars", "perItem", NULL, $opts)->getReducedValue();
  }

}
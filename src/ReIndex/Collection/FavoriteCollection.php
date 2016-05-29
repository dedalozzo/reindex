<?php

/**
 * @file FavoriteCollection.php
 * @brief This file contains the FavoriteCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Feature\Starrable;
Use ReIndex\Model\Star;
use ReIndex\Helper\Text;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;


/**
 * @brief This class is used to represent a collection of the member's favorite posts.
 * @nosubgrouping
 */
class FavoriteCollection extends FakeCollection {

  /** @name Starring Status */
  //!@{

  const STARRED = 1; //!< The item has been added to the collection.
  const UNSTARRED = 2; //!< The item has been removed from the collection.

  //!@}


  protected function getCount() {
    //! @todo: Implement getCount() method.
  }


  /**
   * @brief Returns `true` if the current item exists in the collection.
   * @param[in] Starrable $item Any class instance who implements the Starrable interface.
   * @param[out] string $starId (optional) The star document ID related to the current post.
   * @retval bool
   */
  public function exists(Starrable $item, &$starId = NULL) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([Text::unversion($item->getId()), $this->user->id]);

    $result = $this->couch->queryView("stars", "perItem", NULL, $opts);

    if ($result->isEmpty())
      return FALSE;
    else {
      $starId = $result[0]['id'];
      return TRUE;
    }
  }


  /**
   * @brief Adds or removes the item to the collection.
   * @param[in] Starrable $item Any class instance who implements the Starrable interface.
   * @retval bool
   */
  public function star(Starrable $item) {
    $starId = NULL;

    if ($this->exists($item, $starId)) {
      $star = $this->couch->getDoc(Couch::STD_DOC_PATH, $starId);
      $this->couch->deleteDoc(Couch::STD_DOC_PATH, $starId, $star->rev);
      return self::UNSTARRED;
    }
    else {
      $doc = Star::create($this->user, $item);
      $this->couch->saveDoc($doc);
      return self::STARRED;
    }
  }

}
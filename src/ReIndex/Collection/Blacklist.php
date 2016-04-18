<?php
/**
 * @file Blacklist.php
 * @brief This file contains the Blacklist class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


class Blacklist extends AbstractCollection {

  const NAME = "blacklist";


  public function exists(Member $member, &$blackId) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([$this->id, $member->id]);

    $result = $this->couch->queryView("blacklist", "perMember", NULL, $opts);

    if ($result->isEmpty())
      return FALSE;
    else {
      $blackId = $result[0]['id'];
      return TRUE;
    }
  }


  public function add(Member $member) {

  }


  public function remove(Member $member) {

  }

}
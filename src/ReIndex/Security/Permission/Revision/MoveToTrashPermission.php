<?php

/**
 * @file Revision/MoveToTrashPermission.php
 * @brief This file contains the MoveToTrashPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Revision;


use ReIndex\Doc\Revision;
use ReIndex\Enum\State;

use EoC\Couch;


/**
 * @brief Permission to delete a revision.
 */
class MoveToTrashPermission extends AbstractPermission {

  /**
   * @var Couch $couch
   */
  protected $couch;


  /**
   * @brief Constructor.
   * @param[in] Doc::Revision $revision
   */
  public function __construct(Revision $revision) {
    parent::__construct($revision);
    $this->couch = $this->di['couchdb'];
  }


  /**
   * @brief A member can delete his own revisions.
   * @retval bool
   */
  public function checkForMemberRole() {
    return $this->user->match($this->revision->creatorId) &&
           ($this->revision->state->is(State::DRAFT) || $this->revision->state->is(State::CURRENT))
      ? TRUE : FALSE;
  }


  /**
   * @brief A moderator can delete any current revision, unless the content has been created by a superior or equal role.
   * @retval bool
   */
  public function checkForModeratorRole() {
    if ($this->checkForMemberRole())
      return TRUE;
    else {
      $creator = $this->couch->getDoc('members', Couch::STD_DOC_PATH, $this->revision->creatorId);
      return $this->revision->state->is(State::CURRENT) &&
             !$creator->roles->areSuperiorThan($this->getRole())
        ? TRUE : FALSE;
    }
  }

}
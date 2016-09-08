<?php

/**
 * @file Question/RestorePermission.php
 * @brief This file contains the RestorePermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Post\Question;


use ReIndex\Doc\Question;
use ReIndex\Enum\State;

use EoC\Couch;


/**
 * @brief Permission to restore a deleted content.
 */
class RestorePermission extends AbstractPermission  {

  /**
   * @var Couch $couch
   */
  protected $couch;


  /**
   * @copydoc AbstractPermission::__construct
   */
  public function __construct(Question $question) {
    parent::__construct($question);
    $this->couch = $this->di['couchdb'];
  }


  /**
   * @brief Permission to restore a deleted content
   */
  public function getDescription() {
    return "Permission to restore a deleted question.";
  }


  /**
   * @brief A moderator (or a member with a superior role) can restore a content, but only if the content has been
   * deleted by a member with an inferior role or by himself.
   */
  public function checkForModeratorRole() {
    if (!$this->question->state->is(State::DELETED))
      return FALSE;
    elseif ($this->question->dustmanId == $this->user->id)
      return TRUE;
    else {
      $dustman = $this->couch->getDoc('members', Couch::STD_DOC_PATH, $this->question->dustmanId);
      return !$dustman->roles->areSuperiorThan($this->getRole()) ? TRUE : FALSE;
    }
  }


}
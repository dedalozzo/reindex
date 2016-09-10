<?php

/**
 * @file Post/RestorePermission.php
 * @brief This file contains the RestorePermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Post;


use ReIndex\Doc\Post;
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
  public function __construct(Post $post) {
    parent::__construct($post);
    $this->couch = $this->di['couchdb'];
  }


  /**
   * @brief A moderator (or a member with a superior role) can restore a content, but only if the content has been
   * deleted by a member with an inferior role or by himself.
   */
  public function checkForModeratorRole() {
    if (!$this->post->state->is(State::DELETED))
      return FALSE;
    elseif ($this->post->dustmanId == $this->user->id)
      return TRUE;
    else {
      $dustman = $this->couch->getDoc('members', Couch::STD_DOC_PATH, $this->post->dustmanId);
      return !$dustman->roles->areSuperiorThan($this->getRole()) ? TRUE : FALSE;
    }
  }


}
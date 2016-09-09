<?php

/**
 * @file Post/MoveToTrashPermission.php
 * @brief This file contains the MoveToTrashPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Post;


use ReIndex\Doc\Post;
use ReIndex\Enum\State;

use EoC\Couch;


/**
 * @brief Permission to delete a post.
 */
class MoveToTrashPermission extends AbstractPermission {

  /**
   * @var Couch $couch
   */
  protected $couch;


  /**
   * @brief Constructor.
   * @param[in] Doc::Post $post
   */
  public function __construct(Post $post) {
    parent::__construct($post);
    $this->couch = $this->di['couchdb'];
  }


  /**
   * @brief A member can delete his own posts.
   * @retval bool
   */
  public function checkForMemberRole() {
    return $this->user->match($this->post->creatorId) &&
           ($this->post->state->is(State::DRAFT) || $this->post->state->is(State::CURRENT))
      ? TRUE : FALSE;
  }


  /**
   * @brief A moderator can delete any current post, unless the content has been created by a superior or equal role.
   * @retval bool
   */
  public function checkForModeratorRole() {
    if ($this->checkForMemberRole())
      return TRUE;
    else {
      $creator = $this->couch->getDoc('members', Couch::STD_DOC_PATH, $this->post->creatorId);
      return $this->post->state->is(State::CURRENT) &&
             !$creator->roles->areSuperiorThan($this->getRole())
        ? TRUE : FALSE;
    }
  }

}
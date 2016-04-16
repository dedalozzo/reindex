<?php
/**
 * @file ViewPostPermission.php
 * @brief This file contains the ${CLASS_NAME} class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Member;


use ReIndex\Model\Post;


class ViewPostPermission {

  protected $post;


  public function __construct(Post $post = NULL) {
    $this->post = $post;
  }


  public function getDescription() {
    //! @todo: Implement getDescription() method.
  }


  /**
   * @brief Returns `true` if the post can be viewed by the current user, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if ($this->post->state->isCurrent()) return TRUE;

    elseif ($this->user->match($this->creatorId)) return TRUE;

    elseif ($this->user->isEditor() && $this->approved()) return TRUE;

    elseif ($this->user->isModerator() &&
      ($this->isSubmittedForPeerReview() or
        $this->isReturnedForRevision() or
        $this->isRejected() or
        $this->isMovedToTrash()))
      return TRUE;
    else
      return FALSE;
  }

}
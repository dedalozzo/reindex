<?php

/**
 * @file Article.php
 * @brief This file contains the Article class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use ReIndex\Security\Permission\Revision\Post\Article as Permission;


/**
 * @brief This class represents a blog article.
 * @nosubgrouping
 */
final class Article extends Post {


  /**
   * @copydoc Revision::approve()
   */
  public function approve() {
    $this->castVoteForPeerReview(new Permission\ApprovePermission($this));
  }


  /**
   * @copydoc Revision::reject()
   */
  public function reject($reason) {
    $this->castVoteForPeerReview(new Permission\RejectPermission($this), FALSE, $reason);
  }

}
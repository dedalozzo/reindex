<?php
/**
 * @file ReturnForRevisionVersionPermission.php
 * @brief This file contains the ReturnForRevisionVersionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Reviewer;


class ReturnForRevisionVersionPermission {


  public function getDescription() {
    //! @todo: Implement getDescription() method.
  }


  /**
   * @brief Returns `true` if the member can ask the original author to revise the document, `false` otherwise.
   * @retval bool
   */
  public function canBeReturnedForRevision() {
    if ($this->isReturnedForRevision()) return FALSE;

    if (($this->user->isModerator() && $this->isSubmittedForPeerReview()) or
      ($this->user->isAdmin() && $this->isCurrent()))
      return TRUE;
    else
      return FALSE;
  }

}
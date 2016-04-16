<?php
/**
 * @file ApproveVersionPermission.php
 * @brief This file contains the ${CLASS_NAME} class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Reviewer;


class ApproveVersionPermission {


  public function getDescription() {
    //! @todo: Implement getDescription() method.
  }


  /**
   * @brief Returns `true` if the document can be approved, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if ($this->user->isModerator() && ($this->isCreated() or $this->isDraft() or $this->isSubmittedForPeerReview()))
      return TRUE;
    else
      return FALSE;
  }

}
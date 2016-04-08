<?php
/**
 * @file SubmitVersionPermission.php
 * @brief This file contains the ${CLASS_NAME} class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Editor;


class SubmitVersionPermission {

  /**
   * @brief Returns `true` if the document can be submitted for peer review, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if ($this->isSubmittedForPeerReview()) return FALSE;

    if ($this->user->match($this->creatorId) && ($this->isCreated() or $this->isDraft()))
      return TRUE;
    else
      return FALSE;
  }


}
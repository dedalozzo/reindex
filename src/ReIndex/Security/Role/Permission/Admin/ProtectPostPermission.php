<?php
/**
 * @file ProtectPostPermission.php
 * @brief This file contains the ProtectPostPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Admin;


use ReIndex\Security\Role\Permission\Moderator\ProtectPostPermission as Superclass;


class ProtectPostPermission extends Superclass {


  public function getDescription() {
    //! @todo: Implement getDescription() method.
  }


  /**
   * @brief Returns `true` if the protection can be removed from the post, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if (!$this->post->isProtected()) return FALSE;

    if ($this->post->isCurrent() or $this->post->isDraft())
      return TRUE;
    else
      return FALSE;
  }

}
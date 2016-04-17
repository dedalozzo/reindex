<?php
/**
 * @file MoveToTrashVersionPermission.php
 * @brief This file contains the MoveToTrashVersionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\Permission\Editor;


use ReIndex\Security\Role\Permission\AbstractPermission;
use ReIndex\Model\Versionable;


class MoveToTrashVersionPermission extends AbstractPermission{

  public $versionable;


  public function __construct(Versionable $versionable) {
    parent::__construct();
    $this->versionable = $versionable;
  }


  public function getDescription() {
    //! @todo: Implement getDescription() method.
  }


  /**
   * @brief Returns `true` if the document can be moved to trash, `false` otherwise.
   * @retval bool
   */
  public function check() {
    if ($this->versionable->isMovedToTrash()) return FALSE;

    if (($this->user->isModerator() && $this->isCurrent()) or ($this->user->match($this->versionable->creatorId) && $this->versionable->state->isDraft()))
      return TRUE;
    else
      return FALSE;
  }

}
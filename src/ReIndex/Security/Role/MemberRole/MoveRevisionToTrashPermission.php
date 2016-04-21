<?php
/**
 * @file MoveRevisionToTrashPermission.php
 * @brief This file contains the MoveRevisionToTrashPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\MemberRole;


use ReIndex\Security\Role\AbstractPermission;
use ReIndex\Model\Versionable;


class MoveRevisionToTrashPermission extends AbstractPermission{

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

    return ($this->user->match($this->versionable->creatorId) && $this->versionable->state->isDraft()) ? TRUE : FALSE;
  }

}
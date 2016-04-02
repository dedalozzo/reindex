<?php
/**
 * @file ProtectPostPermission.php
 * @brief This file contains the ProtectPostPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Privilege;


use ReIndex\Security\Operation\AbstractPermission;
use ReIndex\Security\Operation\IPermission;
use ReIndex\Model\Post;


class ProtectPostPermission extends AbstractPermission implements IPermission {
  protected $post;


  public function __construct(Post $post) {
    parent::__construct();

    $this->post = $post;
  }


  public function isAllowed() {
    if ($this->post->isProtected()) return FALSE;

    if ($this->user->isModerator() && ($this->post->isCurrent() or $this->post->isDraft()))
      return TRUE;
    else
      return FALSE;
  }

}
<?php

/**
 * @file StarPermission.php
 * @brief This file contains the StarPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Revision\Tag;


use ReIndex\Enum\State;


/**
 * @brief Permission to star (or unstar) a tag.
 */
class StarPermission extends AbstractPermission {


  /**
   * @brief A member can star any current tag.
   * @retval bool
   */
  public function checkForMemberRole() {
    return $this->tag->state->is(State::CURRENT);
  }

}
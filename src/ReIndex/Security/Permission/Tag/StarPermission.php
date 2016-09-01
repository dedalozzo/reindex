<?php

/**
 * @file StarPermission.php
 * @brief This file contains the StarPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Tag;


use ReIndex\Enum\State;


/**
 * @brief Permission to star (or unstar) a tag.
 */
class StarPermission extends AbstractPermission {


  public function getDescription() {
    return "Permission to add or remove a tag from favorites.";
  }


  /**
   * @brief Returns `true` if the tag can be starred (or unstarred), `false` otherwise.
   * @retval bool
   */
  public function checkForMemberRole() {
    return $this->tag->state->is(State::CURRENT);
  }

}
<?php

/**
 * @file MemberRole/StarTagPermission.php
 * @brief This file contains the StarTagPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\MemberRole;


use ReIndex\Security\Role\AbstractPermission;
use ReIndex\Doc\Tag;
use ReIndex\Enum\State;


/**
 * @brief Permission to add or remove a tag to favorites.
 */
class StarTagPermission extends AbstractPermission {


  /**
   * @brief Constructor.
   * @param[in] Tag $context.
   */
  public function __construct(Tag $context) {
    parent::__construct($context);
  }


  public function getDescription() {
    return "Approves the document revision.";
  }


  /**
   * @brief Returns `true` if the tag can be starred (or unstarred), `false` otherwise.
   * @retval bool
   */
  public function check() {
    return $this->context->state->is(State::CURRENT);
  }

}
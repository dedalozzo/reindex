<?php

/**
 * @file ReviewerRole.php
 * @brief This file contains the ReviewerRole class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role;


/**
 * @brief A contents reviewer.
 * @details Having this role a member can vote to approve, rejects, return for revision, every single content
 * modification.
 * @nosubgrouping
 */
class ReviewerRole extends EditorRole {


  public function getDescription() {
    return "A contents reviewer";
  }

}
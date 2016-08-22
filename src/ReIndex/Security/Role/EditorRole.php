<?php

/**
 * @file EditorRole.php
 * @brief This file contains the EditorRole class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role;


/**
 * @brief A contents editor.
 * @details Having this role a member can edit any content. We trust an editor to edit anything in the system
 * without it going through peer review.
 * @nosubgrouping
 */
class EditorRole extends TrustedRole {


  public function getDescription() {
    return "A member capable to edit contents";
  }

}
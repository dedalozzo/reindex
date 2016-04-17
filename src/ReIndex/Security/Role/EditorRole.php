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
 * @details Having this role a member can edit any content. Since the platform uses a peer to peer review system, every
 * single content modification must be approved.
 * @nosubgrouping
 */
class EditorRole extends TrustedRole {


  public function getDescription() {
    return "A member capable to edit contents";
  }

}
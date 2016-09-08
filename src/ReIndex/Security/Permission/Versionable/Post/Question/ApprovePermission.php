<?php

/**
 * @file Question/ApprovePermission.php
 * @brief This file contains the ApprovePermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Post\Question;


use ReIndex\Security\Permission\Versionable\Post\ApprovePermission as Superclass;


/**
 * @brief Permission to vote for the approval of a question's revision.
 */
class ApprovePermission extends Superclass {


  /**
   * @copydoc IPermission::getDescription()
   */
  public function getDescription() {
    return "Approves the question revision.";
  }

}
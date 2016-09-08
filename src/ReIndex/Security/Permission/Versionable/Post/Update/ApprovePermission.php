<?php

/**
 * @file Update/ApprovePermission.php
 * @brief This file contains the ApprovePermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post\Update;


use ReIndex\Security\Permission\Post\ApprovePermission as Superclass;


/**
 * @brief Permission to vote for the approval of a update's revision.
 */
class ApprovePermission extends Superclass {


  /**
   * @copydoc IPermission::getDescription()
   */
  public function getDescription() {
    return "Approves the update's revision.";
  }

}
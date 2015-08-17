<?php

/**
 * @file RevokeCommand.php
 * @brief This file contains the RevokeCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command;


/**
 * @brief Revokes a privilege to a user.
 * @nosubgrouping
 */
class RevokeCommand extends AbstractPrivilegeCommand {


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName('revoke');
    parent::configure();
  }

}
<?php

/**
 * @file GrantCommand.php
 * @brief This file contains the GrantCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command;


/**
 * @brief Grants a privilege to a user.
 * @nosubgrouping
 */
class GrantCommand extends AbstractPrivilegeCommand {


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName('grant');
    parent::configure();
  }

}
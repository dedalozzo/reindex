<?php

/**
 * @file MemberRole/SubmitRevisionPermission.php
 * @brief This file contains the SubmitRevisionPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Role\MemberRole;


use ReIndex\Security\Role\AbstractPermission;
use ReIndex\Model\Versionable;


/**
 * @brief Permission to submit the revision to the Peer Review Committee.
 */
class SubmitRevisionPermission extends AbstractPermission {


  /**
   * @brief Constructor.
   * param[in] Model::Versionable $context
   */
  public function __construct(Versionable $context) {
    parent::__construct($context);
  }


  public function getDescription() {
    return "Submit the revision to the Peer Review Committee.";
  }


  public function check() {
    // todo Implement this method.
  }

}
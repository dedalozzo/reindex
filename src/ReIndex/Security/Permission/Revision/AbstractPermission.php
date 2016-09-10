<?php

/**
 * @file Revision/AbstractPermission.php
 * @brief This file contains the AbstractPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Revisions related permissions
namespace ReIndex\Security\Permission\Revision;


use ReIndex\Security\Permission\AbstractPermission as Superclass;
use ReIndex\Doc\Revision;


abstract class AbstractPermission extends Superclass {

  protected $revision;


  /**
   * @brief Constructor.
   * @param[in] Doc::Revision $revision
   */
  public function __construct(Revision $revision) {
    $this->revision = $revision;
    parent::__construct();
  }

}
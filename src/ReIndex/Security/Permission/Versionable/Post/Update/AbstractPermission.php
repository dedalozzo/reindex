<?php

/**
 * @file Update/AbstractPermission.php
 * @brief This file contains the AbstractPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Versionable\Post\Update;


use ReIndex\Security\Permission\AbstractPermission as Superclass;
use ReIndex\Doc\Update;


abstract class AbstractPermission extends Superclass {

  protected $update;


  /**
   * @brief Constructor.
   * @param[in] Doc::Update $update
   */
  public function __construct(Update $update) {
    $this->update = $update;
    parent::__construct();
  }

}
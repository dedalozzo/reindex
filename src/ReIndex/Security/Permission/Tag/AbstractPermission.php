<?php

/**
 * @file Tag/AbstractPermission.php
 * @brief This file contains the AbstractPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Tag;


use ReIndex\Security\Permission\AbstractPermission as Superclass;
use ReIndex\Doc\Tag;


//! Permissions related to the tags
abstract class AbstractPermission extends Superclass {

  protected $tag;


  /**
   * @brief Constructor.
   * @param[in] Tag $context.
   */
  public function __construct(Tag $tag) {
    parent::__construct();
  }

}
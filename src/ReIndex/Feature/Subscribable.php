<?php

/**
 * @file ISubscribe.php
 * @brief This file contains the ISubscribe interface.
 * @details
 * @author Filippo F. Fadda
 */


//! Interfaces related specific features
namespace ReIndex\Feature;


/**
 * @brief Defines a common interface for all the objects supporting a subsciption mechanism.
 * @nosubgrouping
 */
interface Subscribable {


  /**
   * @brief Gets the document identifier.
   * @retval string
   */
  function getId();

}
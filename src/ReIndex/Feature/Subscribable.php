<?php

/**
 * @file Subscribable.php
 * @brief This file contains the Subscribable interface.
 * @details
 * @author Filippo F. Fadda
 */


//! Interfaces related specific features
namespace ReIndex\Feature;


/**
 * @brief Defines a common interface for all the objects supporting a subscription mechanism.
 * @nosubgrouping
 */
interface Subscribable {


  /**
   * @brief Gets the document identifier.
   * @retval string
   */
  function getId();

}
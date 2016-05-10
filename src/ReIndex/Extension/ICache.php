<?php

/**
 * @file ICache.php
 * @brief This file contains the ICache interface.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Extension;


/**
 * @brief Defines caching methods.
 * @nosubgrouping
 */
interface ICache {

  /** @name Caching Methods */
  //!@{

  /**
   * @brief Adds the document to the cache.
   */
  public function index();

  //!@}

} 
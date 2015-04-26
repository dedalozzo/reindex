<?php

/**
 * @file ICount.php
 * @brief This file contains the ICount interface.
 * @details
 * @author Filippo F. Fadda
 */


//! Namespace of model's extensions.
namespace PitPress\Extension;


/**
 * @brief Defines counting methods.
 * @nosubgrouping
 */
interface ICount {

  /** @name Hits Counting Methods */
  //!@{

  /**
   * @brief Returns the times the item has been viewed.
   * @retval string A formatted number.
   */
  public function getHitsCount();


  /**
   * @brief Increments the times the item has been viewed.
   * @retval int The number of hits.
   */
  public function incHits();

  //!@}

} 
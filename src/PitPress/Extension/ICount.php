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
   * @return string A formatted number.
   */
  public function getHitsCount();


  /**
   * @brief Increments the times the item has been viewed.
   */
  public function incHits();

  //!@}

} 
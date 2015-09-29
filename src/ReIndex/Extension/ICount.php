<?php

/**
 * @file ICount.php
 * @brief This file contains the ICount interface.
 * @details
 * @author Filippo F. Fadda
 */


//! Interfaces and traits to extend the models
namespace ReIndex\Extension;


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
   * @details You can avoid to increment the counter checking if the current user is also the item creator. This is done
   * through the parameter `$userId`.
   * @param[in] string $userId Used to compare with the current user ID.
   * @retval int The number of hits.
   */
  public function incHits($userId = NULL);

  //!@}

} 
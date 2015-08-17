<?php

/**
 * @file IPin.php
 * @brief This file contains the IPin interface.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Extension;


/**
 * @brief Defines pinning methods.
 * @nosubgrouping
 */
interface IPin {

  /** @name Pinning Methods */
  //!@{

  /**
   * @brief Marks the item as important, so the item should be always visible.
   */
  public function pin();


  /**
   * @brief Reverts the item to the normal state.
   */
  public function unpin();


  /**
   * @brief Returns `true` if the item has been pinned.
   */
  public function isPinned();

  //!@}

} 
<?php

/**
 * @file IObserver.php
 * @brief This file contains the IObserver interface.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Observer;


/**
 * @brief Defines an updating interface for objects that should be notified of changes in a subject.
 * @details This declaration differs from the one you can find in the GoF book on design patterns. Here we are using a
 * message queue coupling with a change manager instance.
 * @nosubgrouping
 */
interface IObserver {

  /**
   * @brief Returns an array of messages which the observer is listening to.
   * @retval array
   */
  public function getMessages();


  /**
   * @brief Every time an activity is performed by a user, this method is called for all the interested observers.
   */
  public function update();

}
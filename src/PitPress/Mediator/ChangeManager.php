<?php
/**
 * @file ChangeManager.php
 * @brief This file contains the ChangeManager class.
 * @details
 * @author Filippo F. Fadda
 */


//! PitPress mediators namespace.
namespace PitPress\Mediator;


use PitPress\Observer\IObserver;


/**
 * @brief This class implements the mediator pattern to maintain the relations between subjects and observers.
 * @details This implementation differs from the one you can find in the GoF book on design patterns. Here we are using
 * RabbitMQ, a queue manager, so the change manager listen for messages and then notify those messages to the interested
 * observers.
 * @nosubgrouping
 */
class ChangeManager {


  /**
   * @brief Registers the provided observer to the change manager.
   * @param[in] IObserver $observer An instance of an class implementing the IObserver interface.
   */
  public function register(IObserver $observer) {

  }


  /**
   * @brief Unregisters the provided observer from the change manager.
   * @param[in] IObserver $observer An instance of an class implementing the IObserver interface.
   */
  public function unregister(IObserver $observer) {

  }


  /**
   * @brief Notifies a change to all the observers.
   */
  public function notify() {

  }

}
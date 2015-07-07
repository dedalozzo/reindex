<?php

/**
 * @file Decorator.php
 * @brief This file contains the Decorator class.
 * @details
 * @author Filippo F. Fadda
 */


//! PitPress badge's decorators namespace.
namespace PitPress\Model\Badge\Decorator;


use PitPress\Observer\IObserver;
use PitPress\Model\Badge\Committee;


/**
 * @brief This is the ancestor of all decorators, it's abstract and can't be instantiated.
 * @details Decorator implements the observer pattern.
 * @nosubgrouping
 */
abstract class Decorator implements IObserver {

  protected $committee;


  /**
   * @brief Decorator's constructor.
   * @param[in] Committee $committee The committee instance.
   */
  public function __construct(Committee $committee) {
    $this->committee = $committee;
  }


  /**
   * @brief Returns the decorator's class name.
   * @retval string
   */
  public function getClass() {
    return get_class($this);
  }


  /**
   * @brief Returns the human readable badge's decorator name.
   * @retval string
   */
  abstract public function getName();


  /**
   * @brief Returns a brief description of the decorator.
   * @retval string
   */
  abstract public function getBrief();


  /**
   * @brief The decorator is made by the returned metal.
   * @retval string
   */
  abstract public function getMetal();

}
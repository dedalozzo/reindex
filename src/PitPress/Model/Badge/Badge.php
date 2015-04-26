<?php

/**
 * @file Badge.php
 * @brief This file contains the Badge class.
 * @details
 * @author Filippo F. Fadda
 */


//! PitPress badges namespace.
namespace PitPress\Model\Badge;


//use PitPress\Extension\ISubject;
use PitPress\Model\Storable;


/**
 * @brief This is the ancestor of all badges, it's abstract and can't be instantiated.
 * @details Badge implements the observer pattern.
 * @nosubgrouping
 */
abstract class Badge extends Storable {

  protected $subject;


  /**
   * @brief Creates an instance of the badge.
   * @details This function is used internally.
   * @param[in] string $userId The user ID.
   * @param[in] string $resourceId (optional) A resource ID.
   */
  public function __construct($userId, $resourceId = NULL) {
    parent::__construct();
  }


  /**
   * @brief Returns the badge's name.
   * @retval string
   */
  abstract public function getName();


  /**
   * @brief Returns a brief description of the badge.
   * @retval string
   */
  abstract public function getBrief();


  /**
   * @brief The badge is made by the returned metal.
   * @retval string
   */
  abstract public function getMetal();


  /**
   * @brief Every time an activity is performed by a user, this method is called for all the interested badges.
   * @details Creates an instance of the Gearman client, assign the job to the server. The Gearman server checks for the
   * badge, checks if the user deserve it, assign the badge or withdrawn it.
   */
  public function update() {

  }


  /**
   * @brief Returns `true` if the badge has been already awarded to the user, `false otherwise.
   * @retval bool
   */
  abstract public function exist();


  /**
   * @brief Returns `true` if the user deserves this badge, `false otherwise.
   * @retval bool
   */
  abstract public function deserve();


  /**
   * @brief Awards the current badge.
   */
  public abstract function award();


  /**
   * @brief Withdrawn the badge previously awarded.
   * @details Only some badges might be retired.
   */
  abstract public function withdrawn();



  public function save() {
    $this->meta['supertype'] = 'badge';
    $this->meta['metal'] = $this->getMetal();

    parent::save();
  }

} 
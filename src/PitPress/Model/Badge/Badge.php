<?php

/**
 * @file Badge.php
 * @brief This file contains the Badge class.
 * @details
 * @author Filippo F. Fadda
 */


//! PitPress badges namespace.
namespace PitPress\Model\Badge;


use PitPress\Model\Activity\Activity;
use PitPress\Model\Storable;


/*
 * @brief This is the ancestor of all badges, it's abstract and can't be instantiated.
 * @details Badge implements the observer pattern.
 * @nosubgrouping
 */
abstract class Badge extends Storable {

  protected $subject;


  /*
   * @brief Creates an instance of the badge.
   * @details This function is used internally.
   */
  public static function create(Activity $subject) {
    $obj = new static();
    $obj->subject = $subject;
    return $obj;
  }


  /*
   * @brief Returns the badge's name.
   * @return string
   */
  abstract public function getName();


  /*
   * @brief Returns a brief description of the badge.
   * @return string
   */
  abstract public function getBrief();


  /*
   * @brief The badge is made by the returned metal.
   * @return string
   */
  abstract public function getMetal();


  /*
   * @brief Every time an activity is performed by a user, this method is called for all the interested badges. The
   * badge in fact acts like an observer while the activity is the subject. This is a variant of the observer pattern.
   * subject can update the badge state in relation to his changes.
   */
  public function update(Activity $subject) {

  }


  /*
   * @brief Awards the current badge.
   */
  public abstract function award();


  /*
   * @brief Withdrawn the badge previously awarded.
   * @details Only some badges might be retired.
   */
  public abstract function withdrawn();



  public function save() {
    $this->meta['supertype'] = 'badge';
    $this->meta['metal'] = $this->getMetal();

    parent::save();
  }

} 
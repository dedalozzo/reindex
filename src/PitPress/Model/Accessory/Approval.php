<?php

/*
 * @file Approval.php
 * @brief This file contains the Approval class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Accessory;


use ElephantOnCouch\Doc\Doc;


/*
 * @brief This class is used to keep trace of the accepted answers.
 * @details Every time a user asked a question accept an answer an instance of this class is created and stored in the
 * database.
 * @nosubgrouping
 */
class Approval extends Doc {

  /*
   * @brief Creates an instance of Approval class.
   */
  public static function create($questionId, $answerId, $timestamp = NULL) {
    $instance = new self();

    $instance->meta["questionId"] = $questionId;
    $instance->meta["answerId"] = $answerId;

    if (is_null($timestamp))
      $instance->meta["timestamp"] = time();

    return $instance;
  }

}
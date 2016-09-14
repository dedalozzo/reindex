<?php

/**
 * @file Question.php
 * @brief This file contains the Question class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use ReIndex\Security\Permission\Revision\Post\Question as Permission;


/**
 * @brief A question asked by a member.
 * @nosubgrouping
 */
final class Question extends Post {


  /**
   * @brief Marks a question as duplicate of another question.
   */
  public function markAsDuplicate(Question $question) {

  }

}

<?php

/**
 * @file Comment.php
 * @brief This file contains the Comment class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Forum;


use PitPress\Model\Reply;


/**
 * @brief A comment is only used on questions and answers. A comment is a way to ask more information to the question's
 * author, to be able to provide the right answer to the question. All the comments are listed below the question (or
 * the answer) before any answers is showed.
 * @nosubgrouping
 */
class Comment extends Reply {

  public function save() {
    // We don't use the supertype `reply`, because a comment is different from a reply.
    $this->meta['supertype'] = 'comment';

    // We need to bypass the father's method and call instead the grandpa one. This is done to avoid override the
    // supertype we just assigned.
    parent::save(TRUE);
  }

}



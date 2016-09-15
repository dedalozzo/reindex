<?php

/**
 * @file Comment.php
 * @brief This file contains the Comment class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use ReIndex\Collection;
use ReIndex\Helper;
use ReIndex\Property\TBody;
use ReIndex\Property\TExcerpt;


/**
 * @brief A user's comment.
 * @details A comment may be related to a post (a question, an article or an update) and even to an answer.
 * @nosubgrouping
 *
 * @property string $body
 * @property string $excerpt
 * @property string $html
 *
 */
class Comment extends ActiveDoc {
  use TBody, TExcerpt;

  private $votes; // Casted votes.


  public function __construct() {
    parent::__construct();
    $this->votes = new Collection\VoteCollection($this);
    $this->votes->onCastVote = 'zRegisterVote';
  }


  /**
   * @copydoc ActiveDoc::getDbName()
   */
  protected function getDbName() {
    return 'comments';
  }


  /**
   * @brief Parses the body.
   */
  public function parseBody() {
    $this->html = $this->markdown->parse($this->body);
    $this->excerpt = Helper\Text::truncate(Helper\Text::purge($this->html));
  }


  /**
   * @brief Registers the vote into Redis database.
   * @warning Don't call this function unless you know what are you doing.
   * @param[in] int $value The vote.
   */
  public function zRegisterVote($value) {
    /*
    $date = (new \DateTime())->setTimestamp($this->publishedAt);

    // Marks the start of a transaction block. Subsequent commands will be queued for atomic execution using `exec()`.
    $this->redis->multi();

    $this->zMultipleIncrBy(self::POP_SET . 'comment', $date, $value);

    $uniqueMasters = $this->tags->uniqueMasters();
    foreach ($uniqueMasters as $tagId) {
      $prefix = self::POP_SET . $tagId . '_';
      $this->zMultipleIncrBy($prefix . 'post', $date, $value);
      $this->zMultipleIncrBy($prefix . $this->type, $date, $value);
    }

    // Marks the end of the transaction block.
    $this->redis->exec();
    */
  }


  /**
   * @brief Deletes the comment.
   */
  public function delete() {
    parent::delete();
    parent::save(FALSE);

    // deletes the votes from redis
  }


  /**
   * @copydoc ActiveDoc::save()
   */
  public function save() {
    $this->parseBody();
    $this->save();
  }


  //! @cond HIDDEN_SYMBOLS

  public function getItemId() {
    return $this->meta['itemId'];
  }


  public function issetItemId() {
    return isset($this->meta['itemId']);
  }


  public function setItemId($value) {
    $this->meta['itemId'] = $value;
  }


  public function unsetItemId() {
    if ($this->isMetadataPresent('itemId'))
      unset($this->meta['itemId']);
  }


  public function getVotes() {
    return $this->votes;
  }


  public function issetVotes() {
    return isset($this->votes);
  }

  //! @endcond


}



<?php

/**
 * @file IndexTagTask.php
 * @brief This file contains the IndexPostTask class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Task;


use ReIndex\Model\Tag;
use ReIndex\Model\Post;

use Phalcon\Di;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use Monolog\Logger;


/**
 * @brief This task updates a bunch of Redis sets eventually used to sort posts in many different ways.
 * @nosubgrouping
 */
class IndexTagTask implements ITask {

  private $oldTags; // Original tags.
  private $newTags; // Unique master tags.

  private $id;      // Post's ID.
  private $type;    // Post's type.

  /**
   * @var Di $di
   */
  protected $di;

  /**
   * @var Couch $couch
   */
  protected $couch;

  /**
   * @var \Redis $redis
   */
  protected $redis;

  /**
   * @var Logger $log
   */
  protected $log;

  /**
   * @var Post $post
   */
  protected $post;


  /**
   * @brief Constructor.
   * @param[in] Post $post A post.
   */
  public function __construct(Post $post) {
    $this->post = $post;
    $this->init();
  }


  public function init() {
    $this->di = Di::getDefault();
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];
    $this->log = $this->di['log'];
  }


  public function serialize() {
    return serialize($this->post->id);
  }


  public function unserialize($serialized) {
    $this->init();
    $this->post = $this->couch->getDoc(Couch::STD_DOC_PATH, unserialize($serialized));
  }


  /**
   * @brief Adds an ID, using the provided score, to multiple sets of the Redis db.
   * @param[in] string $set The name of the base Redis set.
   * @param[in] \DateTime $date A date.
   * @param[in] string $id The post ID.
   * @param[in] int $score The score.
   */
  private function zMultipleAdd($set, \DateTime $date, $score) {
    $this->redis->zAdd($set, $score, $this->id);
    $this->redis->zAdd($set . $date->format('_Ymd'), $score, $this->id);
    $this->redis->zAdd($set . $date->format('_Ym'), $score, $this->id);
    $this->redis->zAdd($set . $date->format('_Y'), $score, $this->id);
    $this->redis->zAdd($set . $date->format('_Y_w'), $score, $this->id);
  }


  /**
   * @brief Removes an ID from multiple sets of the Redis db.
   * @param[in] string $set The name of the base Redis set.
   * @param[in] \DateTime $date A date.
   * @param[in] string $id The post ID.
   */
  private function zMultipleRem($set, \DateTime $date) {
    $this->redis->del($set . $date->format('_Ymd'));
    $this->redis->del($set . $date->format('_Ym'));
    $this->redis->del($set . $date->format('_Y'));
    $this->redis->del($set . $date->format('_Y_w'));
  }


  /**
   * @brief Adds the post ID, using the provided score, to the specified Redis set.
   * @param[in] string $set The name of the Redis set.
   * @param[in] int $score The score.
   */
  private function zAdd($set, $score) {
    if (!$this->post->isVisible()) return;

    // Order set with all the posts related to a specific tag.
    $this->redis->zAdd($set . $tagId . '_' . 'post', $score, $this->id);

    // Order set with all the posts of a specific type, related to a specific tag.
    $this->redis->zAdd($set . $tagId . '_' . $this->type, $score, $this->id);
  }


  /**
   * @brief Adds the post ID, using the provided score, to the specified Redis set and its related subsets.
   * @param[in] string $set The name of the Redis set.
   * @param[in] \DateTime $date Use this date to create multiple subsets.
   * @param[in] int $score The score.
   */
  private function zAddSpecial($set, \DateTime $date, $score) {
    if (!$this->post->isVisible()) return;

    // Order set with all the posts related to a specific tag.
    $this->zMultipleAdd($set . $tagId . '_' . 'post', $date, $score);

    // Order set with all the post of a specific type, related to a specific tag.
    $this->zMultipleAdd($set . $tagId . '_' . $this->type, $date, $score);
  }


  /**
   * @brief Adds the post to the newest index.
   */
  private function zAddNewest() {
    $this->zAdd(Post::NEW_SET, $this->post->publishedAt);
  }


  /**
   * @brief Adds the post to the popular index.
   */
  private function zAddPopular() {
    $config = $this->di['config'];

    $popularity = ($this->post->getScore() * $config->scoring->voteCoefficient) +
      ($this->post->getRepliesCount() * $config->scoring->replyCoefficient) +
      ($this->post->getHitsCount() * $config->scoring->hitCoefficient);

    $date = (new \DateTime())->setTimestamp($this->post->publishedAt);
    $this->zAddSpecial(Post::POP_SET, $date, $popularity);
  }


  /**
   * @brief Removes the post from the popular index.
   */
  private function zRemPopular() {
    $this->zMultipleRem(Post::POP_SET . $tagId . '_' . 'post', $date);
    $this->zMultipleRem(Post::POP_SET . $tagId . '_' . $this->type, $date);
  }


  /**
   * @brief Adds the post to the active index.
   */
  private function zAddActive() {
    if (!$this->post->isVisible()) return;

    $timestamp = $this->post->getLastUpdate();

    $this->redis->zAdd(Post::ACT_SET . $tagId . '_' . 'post', $timestamp, $this->id);
    $this->redis->zAdd(Post::ACT_SET . 'tags' . '_' . 'post', $timestamp, $tagId);
    $this->redis->zAdd(Post::ACT_SET . $tagId . '_' . $this->type, $timestamp, $this->id);
    $this->redis->zAdd(Post::ACT_SET . 'tags' . '_' . $this->type, $timestamp, $tagId);
  }


  /**
   * @brief Removes the post ID from the indexes.
   */
  protected function deindex() {
    $this->oldTags = $this->post->getOriginalTags();

    $this->zRemPopular();
  }


  /**
   * @brief Adds the post ID to the indexes.
   */
  protected function index() {
    // We are only indexing current versions.
    if (!$this->post->state->isCurrent())
      return;

    $this->newTags = $this->post->tags->uniqueMasters();

    $this->zAddNewest();
    $this->zAddPopular();
    $this->zAddActive();
  }


  public function execute() {
    $this->id = $this->tag->unversionId;

    // Marks the start of a transaction block. Subsequent commands will be queued for atomic execution using `exec()`.
    $this->redis->multi();


    // Newest.
    $this->redis->del(Post::NEW_SET . $tagId . '_' . 'post');
    $this->redis->del(Post::NEW_SET . $tagId . '_' . $this->type);

    // Active.
    $this->redis->del(Post::ACT_SET . $tagId . '_' . 'post');
    $this->redis->del(Post::ACT_SET . 'tags' . '_' . 'post');
    $this->redis->del(Post::ACT_SET . $tagId . '_' . $this->type);
    $this->redis->del(Post::ACT_SET . 'tags' . '_' . $this->type);

    // Popular.



    $opts = new ViewQueryOpts();
    $opts->setKey($tag->unversionId)->doNotReduce();
    $this->couch->queryView('posts', 'perTag', NULL, $opts, $this);


    $this->redis->exec();
  }

}
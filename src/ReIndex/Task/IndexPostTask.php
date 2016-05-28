<?php

/**
 * @file IndexPostTask.php
 * @brief This file contains the IndexPostTask class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Task;


use ReIndex\Model\Post;

use Phalcon\Di;


/**
 * @brief
 * @nosubgrouping
 */
class IndexPostTask implements ITask {

  /** @name Redis Set Names */
  //!@{

  const NEW_SET = 'new_'; //!< Newest posts Redis set.
  const POP_SET = 'pop_'; //!< Popular posts Redis set.
  const ACT_SET = 'act_'; //!< Active posts Redis set.
  const OPN_SET = 'opn_'; //!< Open questions Redis set.

  //!@}


  private $post;

  protected $di; // Stores the default Dependency Injector.
  protected $couch; // Stores the Elephant on Couch Client instance.
  protected $redis; // Stores the Redis client instance.
  protected $log; // Store the logger instance.
  protected $user; // Stores the current user.


  /**
   * @brief Constructor.
   * @param[in] Post $post A post.
   */
  public function __construct(Post $post) {
    $this->di = Di::getDefault();
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];
    $this->log = $this->di['log'];

    $this->user = $this->di['guardian']->getUser();

    $this->post = $post;
  }


  /**
   * @brief Adds an ID, using the provided score, to multiple sets of the Redis db.
   * @param[in] string $set The name of the base Redis set.
   * @param[in] \DateTime $date A date.
   * @param[in] string $id The post ID.
   * @param[in] int $score The score.
   */
  private function zMultipleAdd($set, \DateTime $date, $id, $score) {
    $this->redis->zAdd($set, $score, $id);
    $this->redis->zAdd($set . $date->format('_Ymd'), $score, $id);
    $this->redis->zAdd($set . $date->format('_Ym'), $score, $id);
    $this->redis->zAdd($set . $date->format('_Y'), $score, $id);
    $this->redis->zAdd($set . $date->format('_Y_w'), $score, $id);
  }


  /**
   * @brief Removes an ID from multiple sets of the Redis db.
   * @param[in] string $set The name of the base Redis set.
   * @param[in] \DateTime $date A date.
   * @param[in] string $id The post ID.
   */
  private function zMultipleRem($set, \DateTime $date, $id) {
    $this->redis->zRem($set, $id);
    $this->redis->zRem($set . $date->format('_Ymd'), $id);
    $this->redis->zRem($set . $date->format('_Ym'), $id);
    $this->redis->zRem($set . $date->format('_Y'), $id);
    $this->redis->zRem($set . $date->format('_Y_w'), $id);
  }


  /**
   * @brief Adds the post ID, using the provided score, to the specified Redis set.
   * @param[in] string $set The name of the Redis set.
   * @param[in] int $score The score.
   */
  protected function zAdd($set, $score) {
    if (!$this->post->isVisible()) return;

    $id = $this->post->unversionId;
    $type = $this->post->getType();

    // Order set with all the posts.
    $this->redis->zAdd($set . 'post', $score, $id);

    // Order set with all the posts of a specific type.
    $this->redis->zAdd($set . $type, $score, $id);

    if (!$this->post->tags->isEmpty()) {
      $tags = $this->post->tags->uniqueMasters();

      foreach ($tags as $tagId) {
        // Order set with all the posts related to a specific tag.
        $this->redis->zAdd($set . $tagId . '_' . 'post', $score, $id);

        // Order set with all the posts of a specific type, related to a specific tag.
        $this->redis->zAdd($set . $tagId . '_' . $type, $score, $id);
      }
    }
  }


  /**
   * @brief Removes the post ID from the specified Redis set.
   * @param[in] string $set The name of the Redis set.
   */
  protected function zRem($set) {
    $id = $this->post->unversionId;
    $type = $this->post->getType();

    // Order set with all the posts.
    $this->redis->zRem($set.'post', $id);

    // Order set with all the posts of a specific type.
    $this->redis->zRem($set . $type, $id);

    $tags = $this->post->getOriginalTags();
    foreach ($tags as $tagId) {
      // Order set with all the posts related to a specific tag.
      $this->redis->zRem($set . $tagId . '_' . 'post', $id);

      // Order set with all the posts of a specific type, related to a specific tag.
      $this->redis->zRem($set . $tagId . '_' . $type, $id);
    }
  }


  /**
   * @brief Adds the post ID, using the provided score, to the specified Redis set and its related subsets.
   * @param[in] string $set The name of the Redis set.
   * @param[in] \DateTime $date Use this date to create multiple subsets.
   * @param[in] int $score The score.
   */
  protected function zAddSpecial($set, \DateTime $date, $score) {
    if (!$this->post->isVisible()) return;

    $id = $this->post->unversionId;
    $type = $this->post->getType();

    // Order set with all the posts.
    $this->zMultipleAdd($set . 'post', $date, $id, $score);

    // Order set with all the posts of a specific type: article, question, ecc.
    $this->zMultipleAdd($set . $type, $date, $id, $score);

    if (!$this->post->tags->isEmpty()) {
      $tags = $this->post->tags->uniqueMasters();

      foreach ($tags as $tagId) {
        // Order set with all the posts related to a specific tag.
        $this->zMultipleAdd($set . $tagId . '_' . 'post', $date, $id, $score);

        // Order set with all the post of a specific type, related to a specific tag.
        $this->zMultipleAdd($set . $tagId . '_' . $type, $date, $id, $score);
      }
    }
  }


  /**
   * @brief Removes the post ID from the specified Redis set and its related subsets.
   * @param[in] string $set The name of the Redis set.
   * @param[in] \DateTime $date Use this date to create multiple subsets.
   */
  protected function zRemSpecial($set, \DateTime $date) {
    $id = $this->post->unversionId;
    $type = $this->post->getType();

    // Order set with all the posts.
    $this->zMultipleRem($set . 'post', $date, $id);

    // Order set with all the posts of a specific type.
    $this->zMultipleRem($set . $type, $date, $id);

    $tags = $this->post->getOriginalTags();
    foreach ($tags as $tagId) {
      // Order set with all the posts related to a specific tag.
      $this->zMultipleRem($set . $tagId . '_' . 'post', $date, $id);

      // Order set with all the post of a specific type, related to a specific tag.
      $this->zMultipleRem($set . $tagId . '_' . $type, $date, $id);
    }
  }


  /**
   * @brief Adds the post to the newest index.
   */
  public function zAddNewest() {
    $this->zAdd(self::NEW_SET, $this->post->publishedAt);
  }


  /**
   * @brief Removes the post from the newest index.
   */
  public function zRemNewest() {
    $this->zRem(self::NEW_SET);
  }


  /**
   * @brief Adds the post to the popular index.
   */
  public function zAddPopular() {
    $config = $this->di['config'];

    $popularity = ($this->post->getScore() * $config->scoring->voteCoefficient) +
      ($this->post->getRepliesCount() * $config->scoring->replyCoefficient) +
      ($this->post->getHitsCount() * $config->scoring->hitCoefficient);

    $date = (new \DateTime())->setTimestamp($this->post->publishedAt);
    $this->zAddSpecial(self::POP_SET, $date, $popularity);
  }


  /**
   * @brief Removes the post from the popular index.
   */
  public function zRemPopular() {
    $date = (new \DateTime())->setTimestamp($this->post->publishedAt);
    $this->zRemSpecial(self::POP_SET, $date);
  }


  /**
   * @brief Adds the post to the active index.
   */
  public function zAddActive() {
    if (!$this->post->isVisible()) return;

    $id = $this->post->unversionId;
    $type = $this->post->getType();
    $timestamp = $this->post->getLastUpdate();

    // Order set with all the posts.
    $this->redis->zAdd(self::ACT_SET . 'post', $timestamp, $id);

    // Order set with all the posts of a specific type: article, question, ecc.
    $this->redis->zAdd(self::ACT_SET . $type, $timestamp, $id);

    if (!$this->post->tags->isEmpty()) {
      $tags = $this->post->tags->uniqueMasters();

      foreach ($tags as $tagId) {
        // Filters posts which should appear on the home page.

        // Order set with all the posts related to a specific tag.
        $this->redis->zAdd(self::ACT_SET . $tagId . '_' . 'post', $timestamp, $id);

        // Used to get a list of tags recently updated.
        $this->redis->zAdd(self::ACT_SET . 'tags' . '_' . 'post', $timestamp, $tagId);

        // Order set with all the posts of a specific type, related to a specific tag.
        $this->redis->zAdd(self::ACT_SET . $tagId . '_' . $type, $timestamp, $id);

        // Used to get a list of tags, in relation to a specific type, recently updated.
        $this->redis->zAdd(self::ACT_SET . 'tags' . '_' . $type, $timestamp, $tagId);
      }
    }
  }


  /**
   * @brief Removes the post from the active index.
   */
  public function zRemActive() {
    $id = $this->post->unversionId;
    $type = $this->post->getType();

    // Order set with all the posts.
    $this->redis->zRem(self::ACT_SET . 'post', $id);

    // Order set with all the posts of a specific type: article, question, ecc.
    $this->redis->zRem(self::ACT_SET . $type, $id);

    $tags = $this->post->getOriginalTags();
    foreach ($tags as $tagId) {
      // Filters posts which should appear on the home page.

      // Order set with all the posts related to a specific tag.
      $this->redis->zRem(self::ACT_SET . $tagId . '_' . 'post', $id);

      // Used to get a list of tags recently updated.
      $this->redis->zRem(self::ACT_SET . 'tags' . '_' . 'post', $tagId);

      // Order set with all the posts of a specific type, related to a specific tag.
      $this->redis->zRem(self::ACT_SET . $tagId . '_' . $type, $id);

      // Used to get a list of tags, in relation to a specific type, recently updated.
      $this->redis->zRem(self::ACT_SET . 'tags' . '_' . $type, $tagId);
    }
  }


  /**
   * @brief Removes the post ID from the indexes.
   */
  protected function deindex() {
    $this->zRemNewest();
    $this->zRemPopular();
    $this->zRemActive();
  }


  /**
   * @brief Adds the post ID to the indexes.
   */
  protected function index() {
    // We are only indexing current versions.
    if (!$this->post->state->isCurrent())
      return;

    $this->zAddNewest();
    $this->zAddPopular();
    $this->zAddActive();
  }


  /**
   * @brief Performs deindex then reindex.
   */
  protected function reindex() {
    $this->deindex();
    $this->index();
  }


  public function execute() {
    // Marks the start of a transaction block. Subsequent commands will be queued for atomic execution using `exec()`.
    $this->redis->multi();

    $this->reindex();

    $this->redis->exec();
  }

}
<?php

/**
 * @file IndexPostTask.php
 * @brief This file contains the IndexPostTask class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Task;


use ReIndex\Doc\Post;
use ReIndex\Doc\Member;
use ReIndex\Enum\State;

use Phalcon\Di;

use EoC\Couch;

use Surfer\Hook\IChunkHook;

use Monolog\Logger;


/**
 * @brief This task updates a bunch of Redis sets eventually used to sort posts in many different ways.
 * @nosubgrouping
 */
final class IndexPostTask implements ITask, IChunkHook {

  private $remTags; // Tags to be removed from index.
  private $addTags; // Tags to be added to the index.
  private $uniqueMasters; // Tags associated to the post (excluding synonyms).

  private $id;      // Post's ID.
  private $type;    // Post's type.

  private $toIndex;   // The indexing procedure must be performed.
  private $toDeindex; // The post must be removed from the indexes.

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
   * @var Hoedown $markdown
   */
  protected $markdown;

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
    $this->markdown = $this->di['markdown'];
    $this->log = $this->di['log'];
  }


  public function serialize() {
    return serialize($this->post->id);
  }


  public function unserialize($serialized) {
    $this->init();

    $this->post = $this->couch->getDoc('posts', Couch::STD_DOC_PATH, unserialize($serialized));

    $this->toDeindex = $this->toDeindex();
    $this->toIndex = $this->toIndex();
  }


  /**
   * @brief Adds an ID, using the provided score, to multiple sets of the Redis db.
   * @param[in] string $set The name of the base Redis set.
   * @param[in] \DateTime $date A date.
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
   */
  private function zMultipleRem($set, \DateTime $date) {
    $this->redis->zRem($set, $this->id);
    $this->redis->zRem($set . $date->format('_Ymd'), $this->id);
    $this->redis->zRem($set . $date->format('_Ym'), $this->id);
    $this->redis->zRem($set . $date->format('_Y'), $this->id);
    $this->redis->zRem($set . $date->format('_Y_w'), $this->id);
  }


  /**
   * @brief Returns `true` if the post must be removed from the indexes.
   * @return bool
   */
  private function toDeindex() {
    return $this->post->state->is(State::DELETED | State::INDEXING) or $this->post->state->is(State::CURRENT | State::INDEXING);
  }


  /**
   * @brief Returns `true` if the indexing procedure must be performed.
   * @return bool
   */
  private function toIndex() {
    return $this->post->state->is(State::CURRENT) or $this->post->state->is(State::IMPORTED);
  }


  /**
   * @brief Removes the post ID from the indexes.
   * @param[in] \DateTime $date A date time conversion of the publishing timestamp.
   */
  protected function deindex(\DateTime $date) {
    if (!$this->toDeindex)
      return;

    $set = Member::TL_SET . $this->post->creatorId . $date->format('_Y');
    $this->redis->zRem($set, $this->id);

    $this->redis->zRem(Post::NEW_SET.'post', $this->id);
    $this->redis->zRem(Post::NEW_SET.$this->type, $this->id);

    $this->zMultipleRem(Post::POP_SET.'post', $date);
    $this->zMultipleRem(Post::POP_SET.$this->type, $date);

    $this->redis->zRem(Post::ACT_SET.'post', $this->id);
    $this->redis->zRem(Post::ACT_SET.$this->type, $this->id);

    foreach ($this->remTags as $tagId) {
      $forAll = $tagId . '_' . 'post';
      $forSpecificType = $tagId . '_' . $this->type;

      $this->redis->zRem(Post::NEW_SET.$forAll, $this->id);
      $this->redis->zRem(Post::NEW_SET.$forSpecificType, $this->id);

      $this->zMultipleRem(Post::POP_SET.$forAll, $date);
      $this->zMultipleRem(Post::POP_SET.$forSpecificType, $date);

      $this->redis->zRem(Post::ACT_SET.$forAll, $this->id);
      $this->redis->zRem(Post::ACT_SET.$forSpecificType, $this->id);

      $this->redis->zIncrBy(Tag::POP_SET.'post', -1, $tagId);
      $this->redis->zIncrBy(Tag::POP_SET.$this->type, -1, $tagId);
    }

    $this->redis->del($this->post->unversionId . Post::HASH);
  }


  /**
   * @brief Adds the post ID to the indexes.
   * @param[in] \DateTime $date A date time conversion of the publishing timestamp.
   */
  protected function index(\DateTime $date) {
    if (!$this->toIndex)
      return;

    $updatedAt = $this->post->getLastUpdate();
    $publishedAt = $this->post->publishedAt;
    $score = count($this->post->votes);

    $set = Member::TL_SET . $this->post->creatorId . $date->format('_Y');
    $this->redis->zAdd($set, $publishedAt, $this->id);

    $this->redis->zAdd(Post::NEW_SET.'post', $publishedAt, $this->id);
    $this->redis->zAdd(Post::NEW_SET.$this->type, $publishedAt, $this->id);

    $this->redis->zAdd(Post::ACT_SET.'post', $updatedAt, $this->id);
    $this->redis->zAdd(Post::ACT_SET.$this->type, $updatedAt, $this->id);

    // Let's index only the posts with a score > 0.
    if ($score > 0) {
      $this->zMultipleAdd(Post::POP_SET.'post', $date, $score);
      $this->zMultipleAdd(Post::POP_SET.$this->type, $date, $score);
    }

    foreach ($this->addTags as $tagId) {
      $forAll = $tagId . '_' . 'post';
      $forSpecificType = $tagId . '_' . $this->type;

      $this->redis->zAdd(Post::NEW_SET.$forAll, $publishedAt, $this->id);
      $this->redis->zAdd(Post::NEW_SET.$forSpecificType, $publishedAt, $this->id);

      $this->redis->zAdd(Post::ACT_SET.$forAll, $updatedAt, $this->id);
      $this->redis->zAdd(Post::ACT_SET.$forSpecificType, $updatedAt, $this->id);

      if ($score > 0) {
        $this->zMultipleAdd(Post::POP_SET.$forAll, $date, $score);
        $this->zMultipleAdd(Post::POP_SET.$forSpecificType, $date, $score);
      }

      $this->redis->zIncrBy(Post::POP_TAGS_SET.'post', 1, $tagId);
      $this->redis->zIncrBy(Post::POP_TAGS_SET.$this->type, 1, $tagId);

      $tagActSetForAll = Post::ACT_TAGS_SET.'post';
      if ($publishedAt > $this->redis->zScore($tagActSetForAll, $tagId))
        $this->redis->zAdd($tagActSetForAll, $publishedAt, $tagId);

      $tagActSetForType = Post::ACT_TAGS_SET.$this->type;
      if ($publishedAt > $this->redis->zScore($tagActSetForType, $tagId))
        $this->redis->zAdd($tagActSetForType, $publishedAt, $tagId);
    }

    $tags = implode(',', $this->uniqueMasters);
    $this->redis->hMSet($this->id . Post::HASH, ['tags' => $tags]);
  }


  public function execute() {
    $this->id = $this->post->unversionId;
    $this->type = $this->post->getType();

    $date = new \DateTime();

    // In case the post has been imported...
    if ($this->post->state->is(State::IMPORTED)) {
      $this->post->state->set(State::CURRENT | State::INDEXING);
      $this->post->parseBody();
    }

    $date->setTimestamp($this->post->publishedAt);

    $hash = $this->redis->hMGet($this->id . Post::HASH, ['tags']);
    $this->uniqueMasters = $this->post->tags->uniqueMasters();

    $oldTags = is_string($hash['tags']) ? explode(',', $hash['tags']) : [];
    $newTags = $this->uniqueMasters;

    $this->remTags = array_diff($oldTags, $newTags);
    $this->addTags = array_diff($newTags, $oldTags);

    // Marks the start of a transaction block. Subsequent commands will be queued for atomic execution using `exec()`.
    $this->redis->multi();

    $this->deindex($date);
    $this->index($date);

    // Marks the end of the transaction block.
    $this->redis->exec();

    // todo: fare una insert per ogni follower che abbia aggiunto ai preferiti uno qualunque dei tag associati al post,
    // utilizzando un timestamp, così mostro il post unicamente nella timeline degli starrer che hanno seguito il tag
    // successivamente all'ultima modifica al posto stesso (modifiedAt)

    // todo: utilizzare un timestamp, così mostro il post unicamente nella timeline dei follower che hanno seguito
    // l'autore del post successivamente all'ultima modifica al post stesso (modifiedAt).

    // Searches for all the followers of the member who created the post.
    /*
    $opts->reset();
    $opts->doNotReduce()->reverseOrderOfResults();
    $opts->setStartKey([$this->post->creatorId, Couch::WildCard()])->setEndKey([$this->post->creatorId]);

    // followers/perMember/view
    $this->couch->queryView('followers', 'perMember', 'view' NULL, $opts, $this);
    */

    if ($this->post->state->is(State::INDEXING)) {
      $this->post->state->remove(State::INDEXING);
      $this->post->save();
    }
  }


  public function process($chunk) {
    $row = json_decode(trim($chunk, ',\r\n'));

    if (is_null($row))
      return;

    $set = Member::HP_SET . $row->key[1];

    if ($this->toIndex) {
      // todo: Add option NX when phpredis will support it.
      $this->redis->zAdd($set, $this->post->modifiedAt, $this->post->unversionId);
    }
    else {
      $this->redis->zRem($set, $this->post->unversionId);
    }
  }

}
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
use ReIndex\Helper;

use Phalcon\Di;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;
use EoC\Hook\IChunkHook;

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

    $this->post = $this->couch->getDoc(Couch::STD_DOC_PATH, unserialize($serialized));

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
   * @brief Adds the post ID, using the provided score, to the specified Redis set.
   * @param[in] string $set The name of the Redis set.
   * @param[in] int $score The score.
   */
  private function zAdd($set, $score) {
    // Order set with all the posts.
    $this->redis->zAdd($set . 'post', $score, $this->id);

    // Order set with all the posts of a specific type.
    $this->redis->zAdd($set . $this->type, $score, $this->id);

    foreach ($this->addTags as $tagId) {
      // Order set with all the posts related to a specific tag.
      $this->redis->zAdd($set . $tagId . '_' . 'post', $score, $this->id);

      // Order set with all the posts of a specific type, related to a specific tag.
      $this->redis->zAdd($set . $tagId . '_' . $this->type, $score, $this->id);
    }
  }


  /**
   * @brief Removes the post ID from the specified Redis set.
   * @param[in] string $set The name of the Redis set.
   */
  private function zRem($set) {
    // Order set with all the posts.
    $this->redis->zRem($set.'post', $this->id);

    // Order set with all the posts of a specific type.
    $this->redis->zRem($set . $this->type, $this->id);

    foreach ($this->remTags as $tagId) {
      // Order set with all the posts related to a specific tag.
      $this->redis->zRem($set . $tagId . '_' . 'post', $this->id);

      // Order set with all the posts of a specific type, related to a specific tag.
      $this->redis->zRem($set . $tagId . '_' . $this->type, $this->id);
    }
  }


  /**
   * @brief Adds the post ID, using the provided score, to the specified Redis set and its related subsets.
   * @param[in] string $set The name of the Redis set.
   * @param[in] \DateTime $date Use this date to create multiple subsets.
   * @param[in] int $score The score.
   */
  private function zAddSpecial($set, \DateTime $date, $score) {
    // Order set with all the posts.
    $this->zMultipleAdd($set . 'post', $date, $score);

    // Order set with all the posts of a specific type: article, question, ecc.
    $this->zMultipleAdd($set . $this->type, $date, $score);

    foreach ($this->addTags as $tagId) {
      // Order set with all the posts related to a specific tag.
      $this->zMultipleAdd($set . $tagId . '_' . 'post', $date, $score);

      // Order set with all the post of a specific type, related to a specific tag.
      $this->zMultipleAdd($set . $tagId . '_' . $this->type, $date, $score);
    }
  }


  /**
   * @brief Removes the post ID from the specified Redis set and its related subsets.
   * @param[in] string $set The name of the Redis set.
   * @param[in] \DateTime $date Use this date to create multiple subsets.
   */
  private function zRemSpecial($set, \DateTime $date) {
    // Order set with all the posts.
    $this->zMultipleRem($set . 'post', $date);

    // Order set with all the posts of a specific type.
    $this->zMultipleRem($set . $this->type, $date);

    foreach ($this->remTags as $tagId) {
      // Order set with all the posts related to a specific tag.
      $this->zMultipleRem($set . $tagId . '_' . 'post', $date);

      // Order set with all the post of a specific type, related to a specific tag.
      $this->zMultipleRem($set . $tagId . '_' . $this->type, $date);
    }
  }


  /**
   * @brief Adds the post to the newest index.
   */
  private function zAddNewest() {
    $this->zAdd(Post::NEW_SET, $this->post->publishedAt);
  }


  /**
   * @brief Removes the post from the newest index.
   */
  private function zRemNewest() {
    $this->zRem(Post::NEW_SET);
  }


  /**
   * @brief Adds the post to the popular index.
   * @param[in] \DateTime $date A date time conversion of the publishing timestamp.
   */
  private function zAddPopular(\DateTime $date) {
    $score = count($this->post->votes);
    // Let's index only the posts with a score > 0.
    if ($score > 0)
      $this->zAddSpecial(Post::POP_SET, $date, $score);
  }


  /**
   * @brief Removes the post from the popular index.
   * @param[in] \DateTime $date A date time conversion of the publishing timestamp.
   */
  private function zRemPopular(\DateTime $date) {
    $this->zRemSpecial(Post::POP_SET, $date);
  }


  /**
   * @brief Adds the post to the active index.
   */
  private function zAddActive() {
    $timestamp = $this->post->getLastUpdate();

    // Order set with all the posts.
    $this->redis->zAdd(Post::ACT_SET . 'post', $timestamp, $this->id);

    // Order set with all the posts of a specific type: article, question, ecc.
    $this->redis->zAdd(Post::ACT_SET . $this->type, $timestamp, $this->id);

    foreach ($this->addTags as $tagId) {
      // Order set with all the posts related to a specific tag.
      $this->redis->zAdd(Post::ACT_SET . $tagId . '_' . 'post', $timestamp, $this->id);

      // Order set with all the posts of a specific type, related to a specific tag.
      $this->redis->zAdd(Post::ACT_SET . $tagId . '_' . $this->type, $timestamp, $this->id);

      // Used to get a list of tags recently updated.
      $this->redis->zAdd(Post::ACT_SET . 'tags' . '_' . 'post', $timestamp, $tagId);

      // Used to get a list of tags, in relation to a specific type, recently updated.
      $this->redis->zAdd(Post::ACT_SET . 'tags' . '_' . $this->type, $timestamp, $tagId);

      // Increments the tag's popularity.
      $this->redis->zIncrBy(Post::POP_SET . 'tags', +1, $tagId);
    }
  }


  /**
   * @brief Removes the post from the active index.
   */
  private function zRemActive() {
    // Order set with all the posts.
    $this->redis->zRem(Post::ACT_SET . 'post', $this->id);

    // Order set with all the posts of a specific type: article, question, ecc.
    $this->redis->zRem(Post::ACT_SET . $this->type, $this->id);

    foreach ($this->remTags as $tagId) {
      // Order set with all the posts related to a specific tag.
      $this->redis->zRem(Post::ACT_SET . $tagId . '_' . 'post', $this->id);

      // Order set with all the posts of a specific type, related to a specific tag.
      $this->redis->zRem(Post::ACT_SET . $tagId . '_' . $this->type, $this->id);

      // Decrements the tag's popularity.
      $this->redis->zIncrBy(Post::POP_SET . 'tags', -1, $tagId);
    }
  }


  /**
   * @brief Returns `true` if the post must be removed from the indexes.
   * @return bool
   */
  private function toDeindex() {
    if ($this->post->state->is(State::DELETING) || $this->post->state->is(State::CURRENT) || ($this->post->state->is(State::INDEXING) && isset($this->post->publishedAt)))
      return TRUE;
    else
      return FALSE;
  }


  /**
   * @brief Returns `true` if the indexing procedure must be performed.
   * @return bool
   */
  private function toIndex() {
    if ($this->post->state->is(State::INDEXING) || $this->post->state->is(State::CURRENT))
      return TRUE;
    else
      return FALSE;
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

    $this->zRemNewest();
    $this->zRemPopular($date);
    $this->zRemActive();

    $this->redis->del($this->post->unversionId . Post::PT_HASH);
  }


  /**
   * @brief Adds the post ID to the indexes.
   * @param[in] \DateTime $date A date time conversion of the publishing timestamp.
   */
  protected function index(\DateTime $date) {
    if (!$this->toIndex)
      return;

    $set = Member::TL_SET . $this->post->creatorId . $date->format('_Y');
    $this->redis->zAdd($set, $this->post->publishedAt, $this->id);

    $this->zAddNewest();
    $this->zAddPopular($date);
    $this->zAddActive();

    $tags = implode(',', $this->uniqueMasters);
    $this->redis->hMSet($this->id . Post::PT_HASH, ['tags' => $tags]);
  }


  public function execute() {
    $this->id = $this->post->unversionId;
    $this->type = $this->post->getType();

    $date = new \DateTime();

    if ($this->post->state->is(State::INDEXING)) {
      // Sets the state of the current revision to `approved`.
      $opts = new ViewQueryOpts();
      $opts->doNotReduce()->setKey($this->id);
      $rows = $this->couch->queryView("posts", "unversion", NULL, $opts);

      if (!$rows->isEmpty()) {
        $current = $this->couch->getDoc(Couch::STD_DOC_PATH, $rows[0]['id']);
        $current->state->set(State::APPROVED);
        $current->save();
      }

      if (isset($this->post->title))
        $this->post->slug = Helper\Text::slug($this->post->title);
      else
        $this->post->slug = $this->post->unversionId;

      if (isset($this->post->body)) {
        $this->post->html = $this->markdown->parse($this->post->body);
        $purged = Helper\Text::purge($this->post->html);
        $this->post->excerpt = Helper\Text::truncate($purged);
      }

      if (!isset($this->post->publishedAt))
        $this->post->publishedAt = time();

      $this->post->state->set(State::CURRENT);
      $this->post->save();

      $date->setTimestamp($this->post->publishedAt);
    }


    $hash = $this->redis->hMGet($this->id . Post::PT_HASH, ['tags']);
    $this->uniqueMasters = $this->post->tags->uniqueMasters();

    $oldTags = is_array($hash) ? sort(explode(',', $hash['tags']), SORT_STRING) : [];
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

    $this->couch->queryView("followers", "perMember", NULL, $opts, $this);
    */
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
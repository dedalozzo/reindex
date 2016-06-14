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

  private $id;        // Tag's ID.
  private $synonymId; // Synonym's ID.

  private $zAddNewPostSet;
  private $zRemNewPostSet;
  private $zAddNewTypeSet;
  private $zRemNewTypeSet;

  private $zAddActPostSet;
  private $zRemActPostSet;
  private $zAddActTypeSet;
  private $zRemActTypeSet;

  private $zAddPopPostSet;
  private $zRemPopPostSet;
  private $zAddPopTypeSet;
  private $zRemPopTypeSet;


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
   * @var Tag $tag
   */
  protected $tag;


  /**
   * @brief Constructor.
   * @param[in] Tag $tag A tag.
   */
  public function __construct(Tag $tag) {
    $this->tag = $tag;
    $this->init();
  }


  public function init() {
    $this->di = Di::getDefault();
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];
    $this->log = $this->di['log'];
  }


  public function serialize() {
    return serialize([$this->id, $this->synonymId]);
  }


  public function unserialize($serialized) {
    $this->init();
    $this->tag = $this->couch->getDoc(Couch::STD_DOC_PATH, unserialize($serialized));
    $this->id
  }


  /**
   * @brief Adds an ID, using the provided score, to multiple sets of the Redis db.
   * @param[in] string $set The name of the base Redis set.
   * @param[in] \DateTime $date A date.
   * @param[in] string $id The post ID.
   * @param[in] int $score The score.
   */
  private function zMultipleAdd($set, \DateTime $date, $score, $id) {
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
   * @brief Adds the post to the newest index.
   */
  private function zAddNewest() {
    $this->zAdd(Post::NEW_SET, $this->post->publishedAt);
  }


  /**
   * @brief Adds the post to the popular index.
   */
  private function zAddPopular($id) {
    $config = $this->di['config'];

    $popularity = ($this->post->getScore() * $config->scoring->voteCoefficient) +
      ($this->post->getRepliesCount() * $config->scoring->replyCoefficient) +
      ($this->post->getHitsCount() * $config->scoring->hitCoefficient);

    $date = (new \DateTime())->setTimestamp($this->post->publishedAt);
    $this->zAddSpecial(Post::POP_SET, $date, $popularity, $id);
  }


  public function execute() {
    $this->id = $this->tag->unversionId;

    $this->newPostSet = Post::NEW_SET . $this->synonymId . '_' . 'post';
    $this->newTypeSet = Post::NEW_SET . $this->synonymId . '_' . $type;


    // Marks the start of a transaction block. Subsequent commands will be queued for atomic execution using `exec()`.
    $this->redis->multi();

    $opts = new ViewQueryOpts();
    $opts->setKey($this->synonymId)->doNotReduce();
    $this->couch->queryView('posts', 'perTag', NULL, $opts, $this);

    $this->redis->exec();
  }


  public function process($chunk) {
    $row = json_decode(trim($chunk, ',\r\n'));

    if (is_null($row))
      return;

    // Post's data.
    $postId = $row->id;
    $postType = $row->type;
    //$date = (new \DateTime())->setTimestamp($this->post->publishedAt);

    $score = $this->redis->zscore($this->zRemNewPostSet, $postId);

    $this->redis->zRem($this->zRemNewPostSet, $postId);
    $this->redis->zAdd($this->zAddNewPostSet, $score, $this->id);

    $this->redis->zRem($this->zRemNewTypeSet, $postId);
    $this->redis->zAdd($this->zAddNewTypeSet, $score, $this->id);


    $this->redis->zRem(Post::ACT_SET . $this->synonymId . '_' . 'post', $postId);
    $this->redis->zRem(Post::ACT_SET . $this->synonymId . '_' . $postType, $postId);

    $this->zMultipleRem(Post::POP_SET . $this->synonymId . '_' . 'post', $date, $postId);
    $this->zMultipleRem(Post::POP_SET . $this->synonymId . '_' . $postType, $date, $postId);

    $this->redis->zRem(Post::POP_SET . $this->synonymId . '_' . 'post', $postId);
    $this->redis->zRem(Post::POP_SET . $this->synonymId . '_' . 'post' . $date->format('_Ymd'), $postId);
    $this->redis->zRem(Post::POP_SET . $this->synonymId . '_' . 'post' . $date->format('_Ym'), $postId);
    $this->redis->zRem(Post::POP_SET . $this->synonymId . '_' . 'post' . $date->format('_Y'), $postId);
    $this->redis->zRem(Post::POP_SET . $this->synonymId . '_' . 'post' . $date->format('_Y_w'), $postId);

    $this->redis->zRem(Post::POP_SET . $postType, $postId);
    $this->redis->zRem(Post::POP_SET . $postType. $date->format('_Ymd'), $postId);
    $this->redis->zRem(Post::POP_SET . $postType. $date->format('_Ym'), $postId);
    $this->redis->zRem(Post::POP_SET . $postType. $date->format('_Y'), $postId);
    $this->redis->zRem(Post::POP_SET . $postType. $date->format('_Y_w'), $postId);




    //$timestamp = $this->post->getLastUpdate();
    $this->redis->zAdd(Post::ACT_SET . $tagId . '_' . 'post', $timestamp, $this->id);
    $this->redis->zAdd(Post::ACT_SET . 'tags' . '_' . 'post', $timestamp, $tagId);
    $this->redis->zAdd(Post::ACT_SET . $tagId . '_' . $this->type, $timestamp, $this->id);
    $this->redis->zAdd(Post::ACT_SET . 'tags' . '_' . $this->type, $timestamp, $tagId);
    $this->redis->zAdd(Post::ACT_SET . $tagId . '_' . 'post', $timestamp, $this->id);

    // Used to get a list of tags recently updated.
    $this->redis->zAdd(Post::ACT_SET . 'tags' . '_' . 'post', $timestamp, $tagId);

    // Order set with all the posts of a specific type, related to a specific tag.
    $this->redis->zAdd(Post::ACT_SET . $tagId . '_' . $this->type, $timestamp, $this->id);

    // Used to get a list of tags, in relation to a specific type, recently updated.
    $this->redis->zAdd(Post::ACT_SET . 'tags' . '_' . $this->type, $timestamp, $tagId);

    $this->zMultipleAdd($set . $tagId . '_' . 'post', $date, $score, $postId);

    $this->redis->zAdd($set, $score, $this->id);
    $this->redis->zAdd($set . $date->format('_Ymd'), $score, $this->id);
    $this->redis->zAdd($set . $date->format('_Ym'), $score, $this->id);
    $this->redis->zAdd($set . $date->format('_Y'), $score, $this->id);
    $this->redis->zAdd($set . $date->format('_Y_w'), $score, $this->id);


    // Order set with all the post of a specific type, related to a specific tag.
    $this->zMultipleAdd($set . $tagId . '_' . $this->type, $date, $score, $postId);

    $this->redis->zAdd($set, $score, $this->id);
    $this->redis->zAdd($set . $date->format('_Ymd'), $score, $this->id);
    $this->redis->zAdd($set . $date->format('_Ym'), $score, $this->id);
    $this->redis->zAdd($set . $date->format('_Y'), $score, $this->id);
    $this->redis->zAdd($set . $date->format('_Y_w'), $score, $this->id);



    $this->zAddNewest();
    $this->zAddPopular();
    $this->zAddActive();
  }

}
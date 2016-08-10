<?php

/**
 * @file SynonymizeTask.php
 * @brief This file contains the SynonymizeTask class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Task;


use ReIndex\Doc\Tag;
use ReIndex\Doc\Post;

use Phalcon\Di;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use Monolog\Logger;


/**
 * @brief This task is queued and executed just in case a tag becomes the synonym of another tag.
 * @details Updates a bunch of Redis sets eventually used to sort posts in many different ways.\n
 * This class implement the IChunkHook interface.
 * @nosubgrouping
 */
final class SynonymizeTask implements ITask {

  private $masterId;  // Tag's ID.
  private $synonymId; // Synonym's ID.

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
   * @brief Constructor.
   * @param[in] string $masterId The master's ID.
   * @param[in] string $synonymId The synonym's ID.
   */
  public function __construct($masterId, $synonymId) {
    $this->init();
    $this->masterId = $masterId;
    $this->synonymId = $synonymId;
  }


  public function init() {
    $this->di = Di::getDefault();
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];
    $this->log = $this->di['log'];
  }


  public function serialize() {
    return serialize([$this->masterId, $this->synonymId]);
  }


  public function unserialize($serialized) {
    $this->init();
    list($this->masterId, $this->synonymId) = unserialize($serialized);
  }


  /**
   * @brief Adds an ID, using the provided score, to multiple sets of the Redis db.
   * @param[in] string $set The name of the base Redis set.
   * @param[in] \DateTime $date A date.
   * @param[in] int $score The score.
   * @param[in] string $id The post ID.
   */
  private function zMultipleAdd($set, \DateTime $date, $score, $id) {
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
    $this->redis->zRem($set . $date->format('_Ymd'), $id);
    $this->redis->zRem($set . $date->format('_Ym'), $id);
    $this->redis->zRem($set . $date->format('_Y'), $id);
    $this->redis->zRem($set . $date->format('_Y_w'), $id);
  }


  public function execute() {
    $set = Post::ACT_TAGS_SET.'post';

    $masterTag = Tag::find($this->masterId);
    $synonymTag = Tag::find($this->synonymId);

    $synonymScore = $this->redis->zScore($set, $this->synonymId);
    $masterScore = $this->redis->zScore($set, $this->masterId);

    if ($synonymScore > $masterScore) {
      $this->redis->zAdd($set, $synonymScore, $this->masterId);
    }

    // Marks the start of a transaction block. Subsequent commands will be queued for atomic execution using `exec()`.
    $this->redis->multi();

    $opts = new ViewQueryOpts();
    $opts->setKey($this->synonymId)->doNotReduce();
    // posts/perTag/view
    $this->couch->queryView('posts', 'perTag', 'view', NULL, $opts, $this);

    $postfix = $this->synonymId . '_' . 'post';

    // Removes all the sets related the synonym.
    $this->redis->del(Post::NEW_SET.$postfix);
    $this->redis->del(Post::ACT_SET.$postfix);
    $this->redis->del(Post::POP_SET.$postfix);

    $types = explode(',', str_replace(' ', '', $this->di['config']->postTypes));

    foreach ($types as $type) {
      $set = Post::ACT_TAGS_SET.$type;

      $synonymScore = $this->redis->zScore($set, $this->synonymId);
      $masterScore = $this->redis->zScore($set, $this->masterId);

      if ($synonymScore > $masterScore)
        $this->redis->zAdd($set, $synonymScore, $this->masterId);

      $this->redis->zRem($set, $this->synonymId);

      $postfix = $this->synonymId . '_' . $type;

      $this->redis->del(Post::NEW_SET.$postfix);
      $this->redis->del(Post::ACT_SET.$postfix);
      $this->redis->del(Post::POP_SET.$postfix);
    }

    $this->redis->exec();

    // Creates a synonym having the (unversioned) ID used for the tag and even the same name.
    $synonym = $synonymTag->castAsSynonym();

    // Adds the newly created synonym to the master's synonyms collection.
    $masterTag->synonyms->add($synonym);

    // Saves the master tag.
    $masterTag->save();

    // Saves the synonym. This will rewrite a document using the same `_id` and `_rev`of the tag casted as synonym.
    $synonym->save();
  }


  public function process($chunk) {
    $row = json_decode(trim($chunk, ',\r\n'));

    if (is_null($row))
      return;

    // Post's data.
    $postId = $row->id;
    $postType = $row->type;

    // Postfixes.
    $forAllMaster = $this->masterId . '_' . 'post';
    $forSpecificTypeMaster = $this->masterId . '_' . $postType;
    $forAllSynonym = $this->synonymId . '_' . 'post';
    $forSpecificTypeSynonym = $this->synonymId . '_' . $postType;

    // Newest posts.
    $publishedAt = $this->redis->zScore(Post::NEW_SET.$forAllSynonym, $postId);

    $this->redis->zAdd(Post::NEW_SET.$forAllMaster, $publishedAt, $postId);
    $this->redis->zAdd(Post::NEW_SET.$forSpecificTypeMaster, $publishedAt, $postId);

    // Active posts.
    $activeAt = $this->redis->zScore(Post::ACT_SET.$forAllSynonym, $postId);

    $this->redis->zAdd(Post::ACT_SET.$forAllMaster, $activeAt, $postId);
    $this->redis->zAdd(Post::ACT_SET.$forSpecificTypeMaster, $activeAt, $postId);

    // Popular posts.
    $popularity = $this->redis->zScore(Post::POP_SET.$forAllSynonym, $postId);
    $date = (new \DateTime())->setTimestamp($publishedAt);

    $this->zMultipleRem(Post::POP_SET.$forAllSynonym, $date, $postId);
    $this->zMultipleAdd(Post::POP_SET.$forAllMaster, $date, $popularity, $postId);

    $this->zMultipleRem(Post::POP_SET.$forSpecificTypeSynonym, $date, $postId);
    $this->zMultipleAdd(Post::POP_SET.$forSpecificTypeMaster, $date, $popularity, $postId);
  }

}
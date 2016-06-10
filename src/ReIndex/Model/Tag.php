<?php

/**
 * @file Tag.php
 * @brief This file contains the Tag class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Model;


use ReIndex\Feature;
use ReIndex\Extension;
use ReIndex\Property;
use ReIndex\Helper;
use ReIndex\Queue\TaskQueue;
use ReIndex\Task\IndexPostTask;

use EoC\Opt\ViewQueryOpts;

use Phalcon\Di;



/**
 * @brief A label used to categorize posts.
 * @details Every post must be tagged with a maximun of five tags.
 * @nosubgrouping
 */
class Tag extends Versionable implements Extension\ICount, Feature\Starrable  {
  use Extension\TCount;
  use Property\TExcerpt, Property\TBody, Property\TDescription;


  /**
   * @var TaskQueue $queue
   */
  protected $queue;


  public function __construct() {
    parent::__construct();

    $this->queue = $this->di['taskqueue'];

    $this->meta['master'] = TRUE;
    $this->meta['synonyms'] = [];
  }


  /**
   * @brief Given a list of IDs, returns the correspondent objects.
   * @retval array
   */
  public static function collect(array $ids) {
    if (empty($ids))
      return [];

    $couch = Di::getDefault()['couchdb'];

    $opts = new ViewQueryOpts();

    // Gets the tags properties.
    $opts->doNotReduce();
    $rows = $couch->queryView("tags", "all", $ids, $opts);

    Helper\ArrayHelper::unversion($ids);

    // Retrieves the number of posts per tag.
    $opts->reset();
    $opts->groupResults()->includeMissingKeys();
    $postsCount = $couch->queryView("posts", "perTag", $ids, $opts);

    $tags = [];
    $tagsCount = count($rows);
    for ($i = 0; $i < $tagsCount; $i++) {
      $tag = new \stdClass();
      $tag->id = $rows[$i]['id'];
      $tag->name = $rows[$i]['value'][0];
      $tag->excerpt = $rows[$i]['value'][1];
      $tag->createdAt = $rows[$i]['value'][2];
      //$entry->whenHasBeenPublished = Helper\Time::when($tags[$i]['value'][2]);
      $tag->postsCount = is_null($postsCount[$i]['value']) ? 0 : $postsCount[$i]['value'];

      $tags[] = $tag;
    }

    return $tags;
  }


  /** @name Synonyms Management Methods */
  //!@{

  private function addMultipleSynonymsAtOnce(array $synonyms) {
    $this->meta['synonyms'] = Helper\ArrayHelper::merge($this->meta['synonyms'], $synonyms);
  }


  /**
   * @brief Returns `true` in case this tag is marked as synonym, `false` otherwise.
   * @retval bool
   */
  public function isSynonym() {
    return !$this->meta['master'];
  }


  /**
   * @brief Transforms the tag into a synonym.
   */
  public function transIntoSynonym() {
    $this->meta['master'] = FALSE;

    if ($this->isMetadataPresent('synonyms'))
      unset($this->meta['synonyms']);
  }


  /**
   * @brief Returns the ids of its synonyms.
   * @retval array An array of strings.
   */
  public function getSynonyms() {
    return (!$this->isSynonym()) ? $this->meta['synonyms'] : [];
  }


  /**
   * @brief Marks the provided tag as synonym of the current tag.
   * @param[in] Tag $tag The tag you want add as synonym to the current tag.
   */
  public function addSynonym(Tag $tag) {
    // You can't add a synonym to a synonym, neither you can add a master to a synonym.
    if ($this->isSynonym() or !$this->state->isCurrent() or $tag->isSynonym() or !$tag->state->isCurrent()) return;

    array_push($this->meta['synonyms'], $tag->unversionId);
    $this->addMultipleSynonymsAtOnce($tag->getSynonyms());

    $tag->transIntoSynonym();
    $tag->save();

    $opts = new ViewQueryOpts();
    $opts->setKey($tag->unversionId)->doNotReduce();
    $result = $this->couch->queryView('posts', 'perTag', NULL, $opts);

    if ($result->isEmpty()) return;

    $ids = array_column($result->asArray(), 'id');

    foreach ($ids as $id) {
      // We use update here but we could use article, question, etc. We don't really care, we just need a subclass
      // of Post, since it's abstract and we can't instantiate it.
      $task = new IndexPostTask(Update::create($id));

      // Enqueues the task.
      $this->queue->add($task);
    }
  }

  //!@}


  /**
   * @brief Saves the tag.
   */
  public function save() {
    if (!$this->isSynonym()) {
      $this->html = $this->markdown->parse($this->body);
      $purged = Helper\Text::purge($this->html);
      $this->excerpt = Helper\Text::truncate($purged);
    }
    else {
      // A synonym doesn't have a body and all that stuff, it has just a name.
      unset($this->body);
      unset($this->html);
      unset($this->excerpt);
    }

    parent::save();
  }


  //! @cond HIDDEN_SYMBOLS

  public function getName() {
    return $this->meta['name'];
  }


  public function issetName() {
    return isset($this->meta['name']);
  }


  public function setName($value) {
    // A tag name can't be changed unless the tag has never been approved.
    if ($this->state->isCreated())
      $this->meta['name'] = $value;
    else
      throw new \RuntimeException("Il nome di un tag non può essere cambiato.");
  }


  public function unsetName() {
    if ($this->isMetadataPresent('name'))
      unset($this->meta['name']);
  }

  //! @endcond

}
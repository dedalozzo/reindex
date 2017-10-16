<?php

/**
 * @file Tag.php
 * @brief This file contains the Tag class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use ReIndex\Property\TExcerpt;
use ReIndex\Property\TDescription;
use ReIndex\Exception;
use ReIndex\Collection;
use ReIndex\Task\SynonymizeTask;
use ReIndex\Enum\State;
use ReIndex\Security\Permission\Revision\Tag as Permission;

use EoC\Opt\ViewQueryOpts;

use Daikengo\Exception\AccessDeniedException;

use ToolBag\Helper;

use Phalcon\Di;


/**
 * @brief A label used to categorize posts.
 * @details Every post must be tagged with a maximun of five tags.
 * @nosubgrouping
 *
 * @cond HIDDEN_SYMBOLS
 *
 * @property int $legacyId
 *
 * @property string $name
 *
 * @property string $excerpt
 *
 * @property string $description
 *
 * @property Collection\SynonymCollection $synonyms
 * @property Collection\SubscriptionCollection $subscriptions
 *
 * @endcond
 *
 */
final class Tag extends Revision {
  use TExcerpt, TDescription;

  private $synonyms;      // Collection of synonyms.
  private $tasks;         // Collection of tasks.


  public function __construct() {
    parent::__construct();

    $this->synonyms = new Collection\SynonymCollection('synonyms', $this->meta);
    $this->tasks = new Collection\TaskCollection('tasks', $this->meta);
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
    // tags/info/view
    $rows = $couch->queryView('tags', 'info', 'view', $ids, $opts);

    Helper\ArrayHelper::unversion($ids);

    // Retrieves the number of posts per tag.
    $opts->reset();
    $opts->groupResults()->includeMissingKeys();
    // posts/perTag/view
    $postsCount = $couch->queryView('posts', 'perTag', 'view', $ids, $opts);

    $tags = [];
    $tagsCount = count($rows);
    for ($i = 0; $i < $tagsCount; $i++) {
      $tag = new \stdClass();
      $tag->id = $rows[$i]['id'];
      $tag->name = $rows[$i]['value'][0];
      $tag->excerpt = $rows[$i]['value'][1];
      $tag->createdAt = $rows[$i]['value'][2];
      //$entry->whenHasBeenPublished = Helper\TimeHelper::when($tags[$i]['value'][2]);
      $tag->postsCount = is_null($postsCount[$i]['value']) ? 0 : $postsCount[$i]['value'];

      $tags[] = $tag;
    }

    return $tags;
  }


  /**
   * @copydoc ActiveDoc::getDbName()
   */
  protected function getDbName() {
    return 'tags';
  }


  /**
   * @copydoc Content::parseBody()
   */
  public function parseBody() {
    if (is_null($this->body))
      return;
    else
      parent::parseBody();
  }


  /** @name Starring Methods */
  //!@{

  /**
   * @brief Adds to the user's favorite the current tag.
   */
  public function star() {
    if (!$this->user->has(new Permission\StarPermission($this)))
      throw new AccessDeniedException("Insufficient privileges or illegal state.");

    $result = $this->user->tags->alter($this->unversionId);
    $this->user->save();
    return $result;
  }


  /**
   * @brief Counts the members who have starred the post.
   * @return int
   */
  public function getStarsCount() {
    $opts = new ViewQueryOpts();
    $opts->setKey($this->unversionId);
    //members/byTag/view
    return $this->couch->queryView('members', 'byTag', 'view', NULL, $opts)->getReducedValue();
  }

  //!@}


  /** @name Synonyms Management Methods */
  //!@{

  /**
   * @brief Transforms the current tag into a synonym.
   * @param[in] Tag $tag The master tag.
   */
  public function markAsSynonymOf(Tag $tag) {
    if (!$this->state->is(State::CURRENT))
      throw new Exception\InvalidStateException('Only the current version of a tag can be synonymized.');

    if ($this->state->is(State::INDEXING))
      throw new Exception\InvalidStateException('The tag has been already marked as synonym.');

    $this->state->set(State::CURRENT | State::INDEXING);
    $this->tasks->add(new SynonymizeTask($tag->unversionId, $this->unversionId));
  }


  /**
   * @brief Returns a synonym having the (unversioned) ID used for the tag and even the same name.
   * @retval Synonym
   */
  public function castAsSynonym() {
    $synonym = Synonym::create($this->unversionId);
    $synonym->rev = $this->rev;
    $synonym->name = $this->name;
    $synonym->setRelatesIds($this->meta['synonyms']);
    return $synonym;
  }

  //!@}


  /**
   * @copydoc Revision::submit()
   */
  public function submit() {
    if (!$this->user->has(new Permission\EditPermission($this)))
      throw new AccessDeniedException("Insufficient privileges or illegal state.");

    parent::submit();
  }


  /**
   * @copydoc ActiveDoc::save()
   */
  public function save($update = TRUE) {
    parent::save($update);
    $this->tasks->enqueueAll();
  }


  //! @cond HIDDEN_SYMBOLS

  public function getName() {
    return $this->meta['name'];
  }


  public function issetName() {
    return isset($this->meta['name']);
  }


  public function setName($value) {
    $this->meta['name'] = $value;
  }


  public function unsetName() {
    if ($this->isMetadataPresent('name'))
      unset($this->meta['name']);
  }


  public function getSynonyms() {
    return $this->synonyms;
  }


  public function issetSynonyms() {
    return isset($this->synonyms);
  }

  //! @endcond

}
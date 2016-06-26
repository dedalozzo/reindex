<?php

/**
 * @file Tag.php
 * @brief This file contains the Tag class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use ReIndex\Property;
use ReIndex\Collection;
use ReIndex\Helper;
use ReIndex\Task\SynonymizeTask;

use EoC\Opt\ViewQueryOpts;

use Phalcon\Di;


/**
 * @brief A label used to categorize posts.
 * @details Every post must be tagged with a maximun of five tags.
 * @nosubgrouping
 */
class Tag extends Versionable {
  use Property\TExcerpt, Property\TBody, Property\TDescription;

  // Collection of synonyms.
  private $synonyms;


  public function __construct() {
    parent::__construct();

    $this->meta['synonyms'] = [];
    $this->synonyms = new Collection\SynonymCollection($this->meta);
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

  /**
   * @brief Returns `true` in case this tag has been marked as synonym, `false` otherwise.
   * @retval bool
   */
  public function synonimizing() {
    return $this->isMetadataPresent('synonymizing');
  }


  /**
   * @brief Transforms the tag into a synonym.
   * @param[in] Tag $tag The master tag.
   */
  public function markAsSynonymOf(Tag $tag) {
    if ($this->synonimizing())
      throw new \RuntimeException('The tag has been alreadt marked as synonym.');

    $this->meta['synonymizing'] = TRUE;
    $this->save();

    $queue = $this->di['taskqueue'];
    $queue->add(new SynonymizeTask($tag->unversionId, $this->unversionId));
  }


  /**
   * @brief Returns a synonym having the (unversioned) ID used for the tag and even the same name.
   * @retval Synonym
   */
  public function castAsSynonym() {
    $synonym = Synonym::create($this->unversionId);
    $synonym->name = $this->name;
    $synonym->setRelatesIds($this->meta['synonyms']);
    return $synonym;
  }

  //!@}


  /**
   * @brief Saves the tag.
   */
  public function save(Tag $synonym = NULL) {
    if (!$this->synonimizing()) {
      $this->html = $this->markdown->parse($this->body);
      $purged = Helper\Text::purge($this->html);
      $this->excerpt = Helper\Text::truncate($purged);
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


  public function getSynonyms() {
    return $this->synonyms;
  }


  public function issetSynonyms() {
    return isset($this->synonyms);
  }

  //! @endcond

}
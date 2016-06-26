<?php

/**
 * @file TagCollection.php
 * @brief This file contains the TagCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Doc\Tag;
use ReIndex\Helper;

use EoC\Opt\ViewQueryOpts;


/**
 * @brief This class is used to represent a collection of tags.
 * @nosubgrouping
 */
class TagCollection extends MetaCollection {

  const NAME = "tags";

  protected $couch; // Stores the CouchDB instance.
  protected $redis; // Stores the Redis instance.


  /**
   * @brief Creates a new collection of tags.
   * @param[in] array $meta Post's array of metadata.
   */
  public function __construct(array &$meta) {
    parent::__construct($meta);

    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];
  }


  /**
   * @brief Adds the specified tag to the list of tags.
   * @attention Don't use this method even if it's public, unless you know what are you doing.
   * @param[in] string $tagId The tag uuid.
   */
  public function add($tagId) {
    $this->meta[static::NAME][] = Helper\Text::unversion($tagId);
  }


  /**
   * @brief Adds many tags at once to the list of tags.
   * @param[in] array $names An array of strings, the tag names.
   */
  public function addMultipleAtOnce(array $names) {
    $names = array_unique($names);

    $opts = new ViewQueryOpts();
    $opts->includeMissingKeys();
    $rows = $this->couch->queryView("tags", "andSynonymsByName", $names, $opts)->asArray();

    foreach ($rows as $row) {

      if (is_null($row['id'])) {
        // A tag hasn't been found, so creates it.
        $tag = Tag::create();
        $tag->name = $row['key'];
        $tag->creatorId = $this->meta['creatorId'];
        $tag->approve();
        $tag->save();

        $this->add($tag->id);
      }
      else
        $this->add($row['id']);

    }
  }


  /**
   * @brief Resolve the synonyms and returns only unique master tags.
   * @retval array
   */
  public function uniqueMasters() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce();
    $masters = $this->couch->queryView("tags", "synonyms", $this->meta[static::NAME], $opts);
    return array_unique(array_column($masters->asArray(), 'value'));
  }


  /**
   * @brief Gets the associated list of tags.
   */
  public function names() {
    if (!$this->isEmpty()) {
      $ids = $this->uniqueMasters();

      $opts = new ViewQueryOpts();
      $opts->doNotReduce();
      return $this->couch->queryView("tags", "allNames", $ids, $opts);
    }
    else
      return [];
  }

}
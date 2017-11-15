<?php

/**
 * @file TagCollection.php
 * @brief This file contains the TagCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Doc\Tag;

use EoC\Opt\ViewQueryOpts;

use Meta\MetaCollection;

use ToolBag\Helper;

use Phalcon\Di;


/**
 * @brief This class is used to represent a collection of tags.
 * @nosubgrouping
 */
final class TagCollection extends MetaCollection {

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
   * @brief Creates a new collection of tags.
   * @param[in] string $name Collection's name.
   * @param[in] array $meta Array of metadata.
   */
  public function __construct($name, array &$meta) {
    parent::__construct($name, $meta);
    $this->di = Di::getDefault();
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];
  }


  /**
   * @brief Adds the specified tag to the list of tags.
   * @attention Don't use this method even if it's public, unless you know what are you doing.\n
   * This method is public just because it's used by a legacy script to import data from an existence database.
   * @param[in] string $unversionId The unversion tag's uuid.
   */
  public function add($unversionId) {
    $this->meta[$this->name][$unversionId] = NULL;
  }


  /**
   * @brief Adds or removes the tag.
   * @attention Don't use this method even if it's public, unless you know what are you doing.\n
   * @param[in] string $unversionId The unversion tag's uuid.
   * @return int Returns `+1` in case the tag has been added, `-1` otherwise.
   */
  public function alter($unversionId) {
    if ($this->exists($unversionId)) {
      unset($this->meta[$this->name][$unversionId]);
      return -1;
    }
    else {
      $this->add($unversionId);
      return +1;
    }
  }


  /**
   * @brief Returns `true` if the tag is already present, `false` otherwise.
   * @param[in] string $unversionId The unversion tag's uuid.
   * @return bool
   */
  public function exists($unversionId) {
    if (array_key_exists($unversionId, $this->meta[$this->name]))
      return TRUE;
    else
      return FALSE;
  }


  /**
   * @brief Adds many tags at once to the list of tags.
   * @param[in] array $names An array of strings, the tag names.
   */
  public function addMultipleAtOnce(array $names) {
    $names = array_unique($names);

    $opts = new ViewQueryOpts();
    $opts->includeMissingKeys();

    // tags/andSynonymsByName/view
    $rows = $this->couch->queryView('tags', 'andSynonymsByName', 'view', $names, $opts)->asArray();

    foreach ($rows as $row) {

      if (is_null($row['id'])) {
        // A tag hasn't been found, so creates it.
        $tag = Tag::create();
        $tag->name = $row['key'];
        $tag->creatorId = $this->meta['creatorId'];
        $tag->submit();

        $this->add(Helper\TextHelper::unversion($tag->id));
      }
      else
        $this->add(Helper\TextHelper::unversion($row['id']));

    }
  }


  /**
   * @brief Resolve the synonyms and returns only unique master tags.
   * @retval array
   */
  public function uniqueMasters() {
    if (!$this->isEmpty()) {
      // tags/synonyms/view
      $masters = $this->couch->queryView('tags', 'synonyms', 'view', array_keys($this->meta[$this->name]))->asArray();
      return array_unique(array_column($masters, 'value'));
    }
    else
      return [];
  }


  /**
   * @brief Gets the associated list of tags.
   */
  public function names() {
    if (!$this->isEmpty()) {
      $ids = $this->uniqueMasters();

      // tags/names/view
      return $this->couch->queryView('tags', 'names', 'view', $ids);
    }
    else
      return [];
  }

}
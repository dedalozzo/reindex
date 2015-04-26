<?php

/**
 * @file Tag.php
 * @brief This file contains the Tag class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model;


use PitPress\Extension;
use PitPress\Property;
use PitPress\Helper;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;


/**
 * @brief A label used to categorize posts.
 * @details Every post must be tagged with a maximun of five tags.
 * @nosubgrouping
 */
class Tag extends Versionable implements Extension\ICount, Extension\IStar {
  use Extension\TCount, Extension\TStar;
  use Property\TExcerpt, Property\TBody, Property\TDescription;


  public function __construct() {
    parent::__construct();
    $this->meta['master'] = TRUE;
    $this->meta['synonyms'] = [];
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
   * @brief Marks the provided tag a synonym of the current tag.
   * @param[in] Tag $tag The tag you want add as synonym to the current tag.
   */
  public function addSynonym(Tag $tag) {
    // You can't add a synonym to a synonym, neither you can add a master to a synonym.
    if ($this->isSynonym() or !$this->isCurrent() or $tag->isSynonym() or !$tag->isCurrent()) return;

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
      $post = $this->couch->getDoc(Couch::STD_DOC_PATH, $id);

      $post->zRemPopularity();
      $post->zAddPopularity();

      $post->zRemLastUpdate();
      $post->zAddLastUpdate($post->getLastVoteTimestamp());
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
    if ($this->isCreated())
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
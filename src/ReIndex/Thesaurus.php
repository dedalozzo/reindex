<?php

/**
 * @file Thesaurus.php
 * @brief This file contains the Thesaurus class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex;


use ReIndex\Doc\Tag;
use ReIndex\Doc\Synonym;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;


/**
 * @brief A thesaurus is a reference work that lists tags grouped together according to similarity of meaning containing
 * synonyms.
 * @nosubgrouping
 */
final class Thesaurus {

  /**
   * @var Couch $couch
   */
  protected $couch;


  /**
   * @brief Adds the provided synonym to the specified tag.
   * @param[in] string $tagName A tag's name.
   * @param[in] string $synonymName A synonym for the tag.
   */
  public function addSynonym($tagName, $synonymName) {
    $opts = new ViewQueryOpts();
    $opts->setLimit(1);
    $opts->setKey($tagName);
    // tags/byName/view
    $rows = $this->couch->queryView('tags', 'byName', 'view', NULL, $opts);

    if ($rows->isEmpty())
      throw new \RuntimeException('Tag not found.');

    $master = Tag::find('tags', current($rows->getIterator())['id']);

    $opts->setKey($synonymName);
    // tags/andSynonymsByName/view
    $rows = $this->couch->queryView('tags', 'andSynonymsByName', 'view', NULL, $opts);

    if ($rows->isEmpty()) {
      $synonym = Synonym::create();
      $synonym->name = $synonymName;
      $synonym->save();
      $master->synonyms->add($synonym);
      $master->save();
    } else {
      $tag = Tag::find('tags', current($rows->getIterator())['id']);

      if ($tag instanceof Tag)
        $tag->markAsSynonymOf($tag);
      else
        throw new \RuntimeException('The synonym already exists.');
    }
  }


  /**
   * @brief Deletes the specified synonym.
   * @param[in] string $synonymName A synonym.
   */
  public function delSynonym($synonymName) {
    $opts = new ViewQueryOpts();
    $opts->setLimit(1);
    $opts->setKey($synonymName);
    // tags/synonymsByName/view
    $rows = $this->couch->queryView('tags', 'synonymsByName', 'view', NULL, $opts);

    if ($rows->isEmpty())
      throw new \RuntimeException('Synonym not found.');

    $synonym = Synonym::find('tags', current($rows->getIterator())['id']);

    $opts->setKey($synonym->id);
    // tags/synonyms/view
    $rows = $this->couch->queryView('tags', 'synonyms', 'view', NULL, $opts);

    // If the synonym is associated to a tag (it should be), removes the association.
    if (!$rows->isEmpty()) {
      $tag = Tag::find('tags', current($rows->getIterator())['value']);
      $tag->synonyms->remove($synonym);
      $tag->save();
    }

    $synonym->delete();
  }


  /**
   * @brief Lists all the synonyms or the ones related to a tag when its name is provided.
   * @param[in] string $tagName (optional) A tag's name.
   * @retval array An array of strings.
   * @todo Fix the result, since must be an array of strings.
   */
  public function listSynonyms($tagName = NULL) {
    $opts = new ViewQueryOpts();

    if (is_null($tagName)) {
      $opts->doNotReduce();
      // tags/synonymsNames/view
      $rows = $this->couch->queryView('tags', 'synonymsNames', 'view', NULL, $opts);
    }
    else {
      $opts->setLimit(1);
      $opts->setKey($tagName);
      // tags/byName/view
      $rows = $this->couch->queryView('tags', 'byName', 'view', NULL, $opts);

      if ($rows->isEmpty())
        throw new \RuntimeException('Tag not found.');

      $tag = Tag::find('tags', current($rows->getIterator())['id']);

      $opts->reset();
      $opts->doNotReduce();
      // tags/synonymsNames/view
      $rows = $this->couch->queryView('tags', 'synonymsNames', 'view', $tag->synonyms->asArray(), $opts);
    }

    return $rows->asArray();
  }

}
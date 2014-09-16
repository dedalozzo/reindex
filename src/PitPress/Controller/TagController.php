<?php

/**
 * @file TagController.php
 * @brief This file contains the TagController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Controller;


use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper;


/**
 * @brief Controller of Tag actions.
 * @nosubgrouping
 */
class TagController extends BaseController {


  protected function getEntries($ids) {
    if (empty($ids))
      return [];

    $opts = new ViewQueryOpts();

    // Gets the tags properties.
    $opts->doNotReduce();
    $tags = $this->couch->queryView("tags", "all", $ids, $opts);

    $this->view->setVar('tagsCount', $tags->getTotalRows()); // todo This must be changed.

    Helper\ArrayHelper::unversion($ids);

    // Retrieves the posts count per tag.
    $opts->reset();
    $opts->groupResults()->includeMissingKeys();
    $postsCount = $this->couch->queryView("tags", "count", $ids, $opts);

    $entries = [];
    $tagsCount = count($tags);
    for ($i = 0; $i < $tagsCount; $i++) {
      $entry = new \stdClass();
      $entry->id = $tags[$i]['id'];
      $entry->name = $tags[$i]['value'][0];
      $entry->excerpt = $tags[$i]['value'][1];
      $entry->whenHasBeenPublished = Helper\Time::when($tags[$i]['value'][2]);
      $entry->postsCount = is_null($postsCount[$i]['value']) ? 0 : $postsCount[$i]['value'];

      $entries[] = $entry;
    }

    return $entries;
  }


  /**
   * @brief Gets the total number of tags.
   */
  protected function getEntriesCount() {
    $count = $this->couch->queryView("tags", "all")->getReducedValue();
    return Helper\Text::formatNumber($count);
  }


  public function initialize() {
    parent::initialize();
    $this->view->pick('views/tag');
  }


  /**
   * @brief Displays the most popular tags.
   * @todo This requires more job. I have to pickup all the classifications grouped by tag, then use a list function to
   * order them by count descending and return just the last 40.
   */
  public function popularAction() {
    $this->view->setVar('title', 'Tags popolari');
  }


  /**
   * @brief Displays the tags sorted by name.
   */
  public function byNameAction() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(40);
    $tags = $this->couch->queryView("tags", "byName", NULL, $opts)->asArray();

    $this->view->setVar('entries', $this->getEntries(array_column($tags, 'id')));
    $this->view->setVar('entriesCount', $this->getEntriesCount());
    $this->view->setVar('title', 'Tags per nome');
  }


  /**
   * @brief Displays the newest tags.
   */
  public function newestAction() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults()->setLimit(40);
    $tags = $this->couch->queryView("tags", "newest", NULL, $opts)->asArray();

    $this->view->setVar('entries', $this->getEntries(array_column($tags, 'id')));
    $this->view->setVar('title', 'Nuovi tags');
  }


  /**
   * @brief Displays the synonyms.
   * @todo I still don't know how to make this one.
   */
  public function synonymsAction() {
    $this->view->setVar('title', 'Sinonimi');
  }


}
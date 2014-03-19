<?php

//! @file ListController.php
//! @brief This file contains the ListController class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper\Time;
use PitPress\Model\User\User;

use Phalcon\Mvc\View;


//! @brief Ancestor controller for any controller displaying posts.
//! @nosubgrouping
abstract class ListController extends SectionController {


  //! @brief Gets a list of tags recently updated.
  protected function getRecentTags() {
    // todo Change this part, getting the classification of the last week, grouped by tagId.
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(60);
    // todo Change newest with newest.
    $classifications = $this->couch->queryView("classifications", "newest", NULL, $opts)['rows'];
    $keys = array_column($classifications, 'value');

    $opts->reset();
    $tags = $this->couch->queryView("tags", "allNames", $keys, $opts)['rows'];

    $opts->reset();
    $opts->includeMissingKeys()->groupResults();
    $postsPerTag = $this->couch->queryView("classifications", "perTag", $keys, $opts)['rows'];

    $recentTags = [];
    for ($i = 0; $i < 60; $i++)
      $recentTags[] = [$tags[$i]['value'], $postsPerTag[$i]['value']];

    return $recentTags;
  }


  //! @brief Builds the post url, given its section, publishing date and slug.
  //! @return string The complete url of the post.
  protected function buildUrl($section, $publishingDate, $slug) {
    return "http://".$section.".".$this->serverName.date('/Y/m/d/', $publishingDate).$slug;
  }


  //! @brief Given a set of keys, retrieves entries.
  protected function getEntries($keys) {
    if (empty($keys))
      return [];

    $opts = new ViewQueryOpts();

    // Posts.
      $opts->doNotReduce();
    $result = $this->couch->queryView("posts", "all", $keys, $opts);
    $posts = $result['rows'];

    // Scores.
    $opts->reset();
    $opts->includeMissingKeys()->groupResults();
    $scores = $this->couch->queryView("votes", "perPost", $keys, $opts)['rows'];

    // Replays.
    $opts->reset();
    $opts->includeMissingKeys()->groupResults();
    $replays = $this->couch->queryView("replays", "perPost", $keys, $opts)['rows'];

    // Users.
    $keys = array_column(array_column($posts, 'value'), 'userId');
    $opts->reset();
    $opts->doNotReduce()->includeMissingKeys();
    $users = $this->couch->queryView("users", "allNames", $keys, $opts)['rows'];

    $entries = [];
    $postCount = count($posts);
    for ($i = 0; $i < $postCount; $i++) {
      $entry = new \stdClass();
      $entry->id = $posts[$i]['id'];

      $properties = &$posts[$i]['value'];
      $entry->title = $properties['title'];
      $entry->excerpt = $properties['excerpt'];
      $entry->url = $this->buildUrl($properties['section'], $properties['publishingDate'], $properties['slug']);
      $entry->publishingType = $properties['publishingType'];
      $entry->whenHasBeenPublished = Time::when($properties['publishingDate']);
      $entry->userId = $properties['userId'];
      $entry->displayName = $users[$i]['value'][0];
      $entry->gravatar = User::getGravatar($users[$i]['value'][1]);
      $entry->hitsCount = $this->redis->hGet($entry->id, 'hits');
      $entry->score = is_null($scores[$i]['value']) ? 0 : $scores[$i]['value'];
      $entry->replaysCount = is_null($replays[$i]['value']) ? 0 : $replays[$i]['value'];

      // Tags.
      $opts->reset();
      $opts->doNotReduce()->setKey($entry->id);
      $classifications = $this->couch->queryView("classifications", "perPost", NULL, $opts)['rows'];

      if (!empty($classifications)) {
        $keys = array_column($classifications, 'value');
        $opts->reset();
        $opts->doNotReduce();
        $entry->tags = &$this->couch->queryView("tags", "allNames", $keys, $opts)['rows'];
      }
      else
        $entry->tags = [];

      $entries[] = $entry;
    }

    return $entries;
  }


  protected function popularEver($section) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey([$section, time(), new \stdClass()])->setEndKey([$section, 0, 0]);

    $rows = $this->couch->queryView('scores', 'perSection', NULL, $opts)['rows'];

    $this->view->setVar('entries', $this->getEntries(array_column($rows, 'value')));
  }


  public function afterExecuteRoute() {
    parent::afterExecuteRoute();

    $this->view->setVar('recentTags', $this->getRecentTags());
  }

}
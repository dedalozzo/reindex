<?php

/**
 * @file ListController.php
 * @brief This file contains the ListController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Controller;


use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper\Time;
use PitPress\Helper\Stat;
use PitPress\Model\User\User;

use Phalcon\Mvc\View;


/*
 * @brief Ancestor controller for any controller displaying posts.
 * @nosubgrouping
 */
abstract class ListController extends BaseController {


  /**
   * @brief Sets the entries count and the relative label to be displayed on the right column.
   * @param[in] string $method The method name to be called on an instance of the `Stat` class.
   * @param[in] string $label The label to be displayed below the entries count.
   */
  protected function stats($method, $label) {
    $stat = new Stat();
    $this->view->setVar('entriesCount', call_user_func(array($stat, $method)));
    $this->view->setVar('entriesLabel', $label);
  }


  /**
   * @brief Gets a list of tags recently updated.
   */
  protected function getRecentTags() {
    // todo Change this part, getting the classification of the last week, grouped by tagId.
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(60);
    // todo Change newest with newest.
    $classifications = $this->couch->queryView("classifications", "newest", NULL, $opts);
    $keys = array_column($classifications->asArray(), 'value');

    $opts->reset();
    $tags = $this->couch->queryView("tags", "allNames", $keys, $opts);

    $opts->reset();
    $opts->includeMissingKeys()->groupResults();
    $postsPerTag = $this->couch->queryView("classifications", "perTag", $keys, $opts);

    $recentTags = [];
    for ($i = 0; $i < 60; $i++)
      $recentTags[] = [$tags[$i]['value'], $postsPerTag[$i]['value']];

    return $recentTags;
  }


  /**
   * @brief Builds the post url, given its section, publishing date and slug.
   * @return string The complete url of the post.
   */
  protected function buildUrl($section, $publishingDate, $slug) {
    return "http://".$section.".".$this->domainName.date('/Y/m/d/', $publishingDate).$slug;
  }


  /**
   * @brief Given a set of keys, retrieves entries.
   */
  protected function getEntries($keys) {
    if (empty($keys))
      return [];

    $opts = new ViewQueryOpts();

    // Posts.
    $opts->doNotReduce();
    $posts = $this->couch->queryView("posts", "all", $keys, $opts);

    // Likes.
    if (isset($this->user)) {
      $opts->reset();
      $opts->doNotReduce()->includeMissingKeys();

      $complex = [];
      foreach ($keys as $postId)
        $complex[] = [$postId, $this->user->id];

      $likes = $this->couch->queryView("votes", "perPostAndUser", $complex, $opts);
    }

    // Scores.
    $opts->reset();
    $opts->includeMissingKeys()->groupResults();
    $scores = $this->couch->queryView("votes", "perPost", $keys, $opts);

    // Replies.
    $opts->reset();
    $opts->includeMissingKeys()->groupResults();
    $replies = $this->couch->queryView("replies", "perPost", $keys, $opts);

    // Users.
    $keys = array_column(array_column($posts->asArray(), 'value'), 'userId');
    $opts->reset();
    $opts->doNotReduce()->includeMissingKeys();
    $users = $this->couch->queryView("users", "allNames", $keys, $opts);

    $entries = [];
    $postCount = count($posts);
    for ($i = 0; $i < $postCount; $i++) {
      $entry = (object)($posts[$i]['value']);
      $entry->id = $posts[$i]['id'];
      $entry->url = $this->buildUrl($entry->section, $entry->publishingDate, $entry->slug);
      $entry->whenHasBeenPublished = Time::when($entry->publishingDate);
      $entry->username = $users[$i]['value'][0];
      $entry->gravatar = User::getGravatar($users[$i]['value'][1]);
      $entry->hitsCount = $this->redis->hGet($entry->id, 'hits');
      $entry->score = is_null($scores[$i]['value']) ? 0 : $scores[$i]['value'];
      $entry->repliesCount = is_null($replies[$i]['value']) ? 0 : $replies[$i]['value'];
      $entry->liked = is_null($this->user) || is_null($likes[$i]['value']) ? FALSE : TRUE;

      // Tags.
      $opts->reset();
      $opts->doNotReduce()->setKey($entry->id);
      $classifications = $this->couch->queryView("classifications", "perPost", NULL, $opts);

      if (!$classifications->isEmpty()) {
        $keys = array_column($classifications->asArray(), 'value');
        $opts->reset();
        $opts->doNotReduce();
        $entry->tags = $this->couch->queryView("tags", "allNames", $keys, $opts);
      }
      else
        $entry->tags = [];

      $entries[] = $entry;
    }

    return $entries;
  }


  /**
   * @brief Displays the entries per date and type.
   */
  protected function perDate($type, $year, $month, $day) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults();

    if (!empty($day))
      $opts->setStartKey([$type, $year, $month, $day, new \stdClass()])->setEndKey(['blog', $year, $month, $day]);
    elseif (!empty($month))
      $opts->setStartKey([$type, $year, $month, new \stdClass()])->setEndKey(['blog', $year, $month]);
    else
      $opts->setStartKey([$type, $year, new \stdClass()])->setEndKey(['blog', $year]);

    $rows = $this->couch->queryView("posts", "byUrl", NULL, $opts);

    $this->view->setVar('entries', $this->getEntries(array_column($rows->asArray(), 'id')));
  }


  protected function popularEver($section) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey([$section, time(), new \stdClass()])->setEndKey([$section, 0, 0]);

    $rows = $this->couch->queryView('scores', 'perSection', NULL, $opts)->asArray();

    $this->view->setVar('entries', $this->getEntries(array_column($rows, 'value')));
  }


  public function afterExecuteRoute() {
    parent::afterExecuteRoute();

    $this->view->setVar('recentTags', $this->getRecentTags());
  }

}
<?php

/**
 * @file ListController.php
 * @brief This file contains the ListController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Controller;


use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper;
use PitPress\Model\User;

use Phalcon\Mvc\View;


/*
 * @brief Ancestor controller for any controller displaying posts.
 * @nosubgrouping
 */
abstract class ListController extends BaseController {

  protected $resultsPerPage;


  /**
   * @brief Given a set of keys, retrieves entries.
   */
  protected function getEntries($ids) {
    if (empty($ids))
      return [];

    $opts = new ViewQueryOpts();

    // Posts.
    $opts->doNotReduce();
    $posts = $this->couch->queryView("posts", "all", $ids, $opts);

    Helper\ArrayHelper::unversion($ids);

    // Likes.
    if (isset($this->user)) {
      $opts->reset();
      $opts->doNotReduce()->includeMissingKeys();

      $keys = [];
      foreach ($ids as $postId)
        $keys[] = [$postId, $this->user->id];

      $likes = $this->couch->queryView("votes", "perItemAndUser", $keys, $opts);
    }

    // Scores.
    $opts->reset();
    $opts->includeMissingKeys()->groupResults();
    $scores = $this->couch->queryView("votes", "perItem", $ids, $opts);

    // Replies.
    $opts->reset();
    $opts->includeMissingKeys()->groupResults();
    $replies = $this->couch->queryView("replies", "perPost", $ids, $opts);

    // Users.
    $userIds = array_column(array_column($posts->asArray(), 'value'), 'userId');
    $opts->reset();
    $opts->doNotReduce()->includeMissingKeys();
    $users = $this->couch->queryView("users", "allNames", $userIds, $opts);

    $entries = [];
    $postCount = count($posts);
    for ($i = 0; $i < $postCount; $i++) {
      $entry = (object)($posts[$i]['value']);
      $entry->id = $posts[$i]['id'];
      $entry->url = $this->buildPostUrl($entry->publishedAt, $entry->slug);
      $entry->whenHasBeenPublished = Helper\Time::when($entry->publishedAt);
      $entry->username = $users[$i]['value'][0];
      $entry->gravatar = User::getGravatar($users[$i]['value'][1]);
      $entry->hitsCount = $this->redis->hGet($entry->id, 'hits');
      $entry->score = is_null($scores[$i]['value']) ? 0 : $scores[$i]['value'];
      $entry->repliesCount = is_null($replies[$i]['value']) ? 0 : $replies[$i]['value'];
      $entry->liked = is_null($this->user) || is_null($likes[$i]['value']) ? FALSE : TRUE;

      // Tags.
      $opts->reset();
      $opts->doNotReduce();
      $entry->tags = (!empty($entry->tags)) ? $this->couch->queryView("tags", "allNames", $entry->tags, $opts) : [];

      $entries[] = $entry;
    }

    return $entries;
  }


  /**
   * @brief Builds the post url, given its publishing date and slug.
   * @param[in] int $publishedAt The publishing timestamp.
   * @param[in] string $slug The slug of the title.
   * @return string The complete url of the post.
   */
  protected function buildPostUrl($publishedAt, $slug) {
    return "http://".$this->domainName.date('/Y/m/d/', $publishedAt).$slug;
  }


  /**
   * @brief Builds the pagination url for CouchDB.
   * @param[in] mixed $startKey A key.
   * @param[in] string $startKeyDocId A document ID.
   * @return string The pagination url.
   */
  protected function buildPaginationUrlForCouch($starKey, $startKeyDocId) {
    return sprintf('%s%s?startkey=%s&startkey_docid=%s', $this->domainName, parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), $starKey, $startKeyDocId);
  }


  /**
   * @brief Builds the pagination url for Redis.
   * @param[in] int $offset The offset.
   * @return string The pagination url.
   */
  protected function buildPaginationUrlForRedis($offset) {
    return sprintf('%s%s?offset=%d', $this->domainName, parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), $offset);
  }


  public function initialize() {
    parent::initialize();

    $this->resultsPerPage = $this->di['config']->application->resultsPerPage;

    $this->assets->addJs("/pit-bootstrap/dist/js/list.min.js", FALSE);
  }


  public function afterExecuteRoute() {
    parent::afterExecuteRoute();
  }

}
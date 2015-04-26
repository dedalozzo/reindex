<?php

/**
 * @file ListController.php
 * @brief This file contains the ListController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Controller;


use EoC\Opt\ViewQueryOpts;

use PitPress\Enum\DocStatus;
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
    if ($this->user->isMember()) {
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
    $creatorIds = array_column(array_column($posts->asArray(), 'value'), 'creatorId');
    $opts->reset();
    $opts->doNotReduce()->includeMissingKeys();
    $users = $this->couch->queryView("users", "allNames", $creatorIds, $opts);

    $entries = [];
    $postCount = count($posts);
    for ($i = 0; $i < $postCount; $i++) {
      $entry = (object)($posts[$i]['value']);
      $entry->id = $posts[$i]['id'];

      if ($entry->status == DocStatus::CURRENT) {
        $entry->url = Helper\Url::build($entry->publishedAt, $entry->slug);
        $entry->timestamp = Helper\Time::when($entry->publishedAt);
      }
      else {
        $entry->url = Helper\Url::build($entry->createdAt, $entry->slug);
        $entry->timestamp = Helper\Time::when($entry->createdAt);
      }

      $entry->username = $users[$i]['value'][0];
      $entry->gravatar = User::getGravatar($users[$i]['value'][1]);
      $entry->hitsCount = Helper\Text::formatNumber($this->redis->hGet(Helper\Text::unversion($entry->id), 'hits'));
      $entry->score = is_null($scores[$i]['value']) ? 0 : $scores[$i]['value'];
      $entry->repliesCount = is_null($replies[$i]['value']) ? 0 : $replies[$i]['value'];
      $entry->liked = $this->user->isGuest() || is_null($likes[$i]['value']) ? FALSE : TRUE;

      if (!empty($entry->tags)) {
        // Tags.
        $opts->reset();
        $opts->doNotReduce();

        // Resolves the synonyms.
        $synonyms = $this->couch->queryView("tags", "synonyms", $entry->tags, $opts);

        // Extracts the masters.
        $masters = array_unique(array_column($synonyms->asArray(), 'value'));

        $entry->tags = $this->couch->queryView("tags", "allNames", $masters, $opts);
      }
      else
        $entry->tags = [];

      $entries[] = $entry;
    }

    return $entries;
  }


  /**
   * @brief Builds the pagination url for CouchDB.
   * @param[in] mixed $startKey A key.
   * @param[in] string $startKeyDocId A document ID.
   * @retval string The pagination url.
   */
  protected function buildPaginationUrlForCouch($startKey, $startKeyDocId) {
    return sprintf('%s%s?startkey=%s&startkey_docid=%s', $this->domainName, parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), $startKey, $startKeyDocId);
  }


  /**
   * @brief Builds the pagination url for Redis.
   * @param[in] int $offset The offset.
   * @retval string The pagination url.
   */
  protected function buildPaginationUrlForRedis($offset) {
    return sprintf('%s%s?offset=%d', $this->domainName, parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), $offset);
  }


  public function initialize() {
    parent::initialize();
  }


  public function afterExecuteRoute() {
    parent::afterExecuteRoute();
  }

}
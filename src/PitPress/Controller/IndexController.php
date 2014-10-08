<?php

/**
 * @file IndexController.php
 * @brief This file contains the IndexController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Controller;


use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper;

use Phalcon\Mvc\View;
use Phalcon\Tag;


/**
 * @brief Controller of Index actions.
 * @nosubgrouping
 */
class IndexController extends ListController {


  protected function getLabel() {
    return 'contributi';
  }


  /**
   * @brief Returns `true`if the caller object is an instance of the class implementing this method, `false` otherwise.
   */
  protected function isSameClass() {
    return get_class($this) == get_class();
  }


  /**
   * @brief Given a tag's name, returns its id.
   * @param[in] string $name The tag's name.
   * @return string|bool Returns the tag id, or `false` in case the tag doesn't exist.
   */
  protected function getTagId($name) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey($name);

    $rows = $this->couch->queryView('tags', 'byName', NULL, $opts);

    if ($rows->count())
      return Helper\Text::unversion(current($rows->getIterator())['id']);
    else
      return FALSE;
  }


  /*
   * @brief Retrieves information for a bunch of posts.
   */
  protected function getInfo($viewName, $type, $count = 10) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit($count)->reverseOrderOfResults()->setStartKey([$type, Couch::WildCard()])->setEndKey([$type]);
    $rows = $this->couch->queryView('posts', $viewName, NULL, $opts);

    if ($rows->isEmpty())
      return NULL;

    // Entries.
    $ids = array_column($rows->asArray(), 'id');

    // Posts.
    $opts->reset();
    $opts->doNotReduce()->includeMissingKeys();
    $posts = $this->couch->queryView("posts", "all", $ids, $opts);

    Helper\ArrayHelper::unversion($ids);

    // Scores.
    $opts->reset();
    $opts->includeMissingKeys()->groupResults();
    $scores = $this->couch->queryView("votes", "perItem", $ids, $opts);

    // Replies.
    $opts->reset();
    $opts->includeMissingKeys()->groupResults();
    $replies = $this->couch->queryView("replies", "perPost", $ids, $opts);

    $entries = [];
    $postCount = count($posts);
    for ($i = 0; $i < $postCount; $i++) {
      $entry = new \stdClass();
      $entry->id = $posts[$i]['id'];

      $properties = $posts[$i]['value'];
      $entry->title = $properties['title'];
      $entry->url = $this->buildPostUrl($properties['publishedAt'], $properties['slug']);
      $entry->whenHasBeenPublished = Helper\Time::when($properties['publishedAt']);
      $entry->score = is_null($scores[$i]['value']) ? 0 : $scores[$i]['value'];
      $entry->repliesCount = is_null($replies[$i]['value']) ? 0 : $replies[$i]['value'];

      $entries[] = $entry;
    }

    return $entries;
  }


  /**
   * @brief Gets a list of tags recently updated.
   */
  protected function recentTags($count = 20) {
    $recentTags = [];

    if ($this->isSameClass())
      $set = "tmp_tags".'_'.'post';
    else
      $set = "tmp_tags".'_'.$this->type;

    $ids = $this->redis->zRevRangeByScore($set, '+inf', 0, ['limit' => [0, $count-1]]);

    if (!empty($ids)) {
      $opts = new ViewQueryOpts();
      $opts->doNotReduce();
      $names = $this->couch->queryView("tags", "allNames", $ids, $opts);

      $opts->reset();
      $opts->groupResults()->includeMissingKeys();
      $posts = $this->couch->queryView("posts", "perTag", $ids, $opts);

      $count = count($ids);
      for ($i = 0; $i < $count; $i++)
        $recentTags[] = [$names[$i]['value'], $posts[$i]['value']];
    }

    $this->view->setVar('recentTags', $recentTags);
  }


  public function initialize() {
    parent::initialize();

    $this->type = $this->controllerName;

    $this->monolog->addDebug(sprintf('Type: %s', $this->type));

    $this->assets->addJs("/pit-bootstrap/dist/js/tab.min.js", FALSE);

    $this->view->pick('views/index');
  }


  public function afterExecuteRoute() {
    parent::afterExecuteRoute();

    $this->recentTags();

    // The entries label is printed below the entries count.
    $this->view->setVar('entriesLabel', $this->getLabel());

    // Those are the notebook pages, printed using the `updates.volt` widget.
    $this->view->setVar('questions', $this->getInfo('perDateByType', 'question'));
    $this->view->setVar('articles', $this->getInfo('perDateByType', 'article'));
    $this->view->setVar('books', $this->getInfo('perDateByType', 'book'));
  }


  /**
   * @brief Page index.
   */
  public function indexAction() {
    if (isset($this->user)) {
      $this->view->setVar('title', 'Home');
      $this->actionName = 'newest';

      return $this->dispatcher->forward(
        [
          'controller' => 'index',
          'action' => 'newest'
        ]);
    }
    else
      return $this->dispatcher->forward(
        [
          'controller' => 'auth',
          'action' => 'logon'
        ]);
  }


  /**
   * @brief Page index by tag.
   * @param[in] string $tag The tag name.
   */
  public function indexByTagAction($tag) {
    $this->actionName = 'newestByTag';

    return $this->dispatcher->forward(
      [
        'controller' => 'index',
        'action' => 'newestByTag',
        'params' => [$tag]
      ]);
  }


  /**
   * @brief Displays information about the tag.
   */
  public function infoByTagAction($tag) {
    $this->view->setVar('title', 'Tags popolari');
  }


  /**
   * @brief Displays the newest posts.
   */
  public function newestAction() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults()->setLimit($this->resultsPerPage+1);

    // Paginates results.
    $startKey = isset($_GET['startkey']) ? (int)$_GET['startkey'] : Couch::WildCard();
    if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    if ($this->isSameClass()) {
      $opts->setStartKey($startKey);
      $rows = $this->couch->queryView("posts", "perDate", NULL, $opts);

      $count = $this->couch->queryView("posts", "perDate")->getReducedValue();
    }
    else {
      $opts->setStartKey([$this->type, $startKey])->setEndKey([$this->type]);
      $rows = $this->couch->queryView("posts", "perDateByType", NULL, $opts);

      $opts->reset();
      $opts->setStartKey([$this->type])->setEndKey([$this->type, Couch::WildCard()]);
      $count = $this->couch->queryView('posts', 'perDateByType', NULL, $opts)->getReducedValue();
    }

    $entries = $this->getEntries(array_column($rows->asArray(), 'id'));

    if (count($entries) > $this->resultsPerPage) {
      $last = array_pop($entries);
      $this->view->setVar('nextPage', $this->buildPaginationUrlForCouch($last->publishedAt, $last->id));
    }

    $this->view->setVar('entries', $entries);
    $this->view->setVar('entriesCount', Helper\Text::formatNumber($count));

    if (is_null($this->view->title))
      $this->view->setVar('title', sprintf('Nuovi %s', $this->getLabel()));
  }


  /**
   * @brief Displays the newest posts by tag.
   * @param[in] string $tag The tag name.
   */
  public function newestByTagAction($tag) {
    $tagId = $this->getTagId(urldecode($tag));
    if ($tagId === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults()->setLimit($this->resultsPerPage+1);

    // Paginates results.
    $startKey = isset($_GET['startkey']) ? (int)$_GET['startkey'] : Couch::WildCard();
    if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    if ($this->isSameClass()) {
      $opts->setStartKey([$tagId, $startKey])->setEndKey([$tagId]);
      $rows = $this->couch->queryView("posts", "perDateByTag", NULL, $opts);

      $opts->reset();
      $opts->setStartKey([$tagId])->setEndKey([$tagId, Couch::WildCard()]);
      $count = $this->couch->queryView('posts', 'perDateByTag', NULL, $opts)->getReducedValue();
    }
    else {
      $opts->setStartKey([$tagId, $this->type, $startKey])->setEndKey([$tagId, $this->type]);
      $rows = $this->couch->queryView("posts", "perDateByTagAndType", NULL, $opts);

      $opts->reset();
      $opts->setStartKey([$tagId, $this->type])->setEndKey([$tagId, $this->type, Couch::WildCard()]);
      $count = $this->couch->queryView('posts', 'perDateByTagAndType', NULL, $opts)->getReducedValue();
    }

    $entries = $this->getEntries(array_column($rows->asArray(), 'id'));

    if (count($entries) > $this->resultsPerPage) {
      $last = array_pop($entries);
      $this->view->setVar('nextPage', $this->buildPaginationUrlForCouch($last->publishedAt, $last->id));
    }

    $this->view->setVar('entries', $entries);
    $this->view->setVar('entriesCount', Helper\Text::formatNumber($count));
    $this->view->setVar('resource', $tag);

    if (is_null($this->view->title))
      $this->view->setVar('title', sprintf('Nuovi %s', $this->getLabel()));
  }


  /**
   * @brief Displays the posts per date.
   */
  public function perDateAction($year, $month = NULL, $day = NULL) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults()->setLimit($this->resultsPerPage+1);

    Helper\Time::dateLimits($minDate, $maxDate, $year, $month, $day);

    // Paginates results.
    $postDate = isset($_GET['startkey']) ? (new \DateTime())->setTimestamp((int)$_GET['startkey']) : clone($maxDate);
    if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    if ($this->isSameClass()) {
      $opts->setStartKey($postDate->getTimestamp())->setEndKey($minDate->getTimestamp());
      $rows = $this->couch->queryView("posts", "perDate", NULL, $opts);

      $opts->reduce()->setStartKey($maxDate->getTimestamp())->unsetOpt('startkey_docid');
      $count = $this->couch->queryView("posts", "perDate", NULL, $opts)->getReducedValue();
    }
    else {
      $opts->setStartKey([$this->type, $postDate->getTimestamp()])->setEndKey([$this->type, $minDate->getTimestamp()]);
      $rows = $this->couch->queryView("posts", "perDateByType", NULL, $opts);

      $opts->reduce()->setStartKey([$this->type, $maxDate->getTimestamp()])->unsetOpt('startkey_docid');
      $count = $this->couch->queryView("posts", "perDateByType", NULL, $opts)->getReducedValue();
    }

    $entries = $this->getEntries(array_column($rows->asArray(), 'id'));

    if (count($entries) > $this->resultsPerPage) {
      $last = array_pop($entries);
      $this->view->setVar('nextPage', $this->buildPaginationUrlForCouch($last->publishedAt, $last->id));
    }

    $this->view->setVar('entries', $entries);
    $this->view->setVar('entriesCount', Helper\Text::formatNumber($count));
    $this->view->setVar('title', sprintf('%s per data', ucfirst($this->getLabel())));
  }


  /**
   * @brief Displays the posts per date by tag.
   * @param[in] string $tag The tag name.
   */
  public function perDateByTagAction($tag, $year, $month = NULL, $day = NULL) {
    $tagId = $this->getTagId($tag);
    if ($tagId === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults()->setLimit($this->resultsPerPage+1);

    Helper\Time::dateLimits($minDate, $maxDate, $year, $month, $day);

    // Paginates results.
    $postDate = isset($_GET['startkey']) ? (new \DateTime())->setTimestamp((int)$_GET['startkey']) : clone($maxDate);
    if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    if ($this->isSameClass()) {
      $opts->setStartKey([$tagId, $postDate->getTimestamp()])->setEndKey([$tagId, $minDate->getTimestamp()]);
      $rows = $this->couch->queryView("posts", "perDateByTag", NULL, $opts);

      $opts->reduce()->setStartKey([$tagId, $maxDate->getTimestamp()])->unsetOpt('startkey_docid');
      $count = $this->couch->queryView("posts", "perDateByTag", NULL, $opts)->getReducedValue();
    }
    else {
      $opts->setStartKey([$tagId, $this->type, $postDate->getTimestamp()])->setEndKey([$tagId, $this->type, $minDate->getTimestamp()]);
      $rows = $this->couch->queryView("posts", "perDateByTagAndType", NULL, $opts);

      $opts->reduce()->setStartKey([$tagId, $this->type, $maxDate->getTimestamp()])->unsetOpt('startkey_docid');
      $count = $this->couch->queryView("posts", "perDateByTagAndType", NULL, $opts)->getReducedValue();
    }

    $entries = $this->getEntries(array_column($rows->asArray(), 'id'));

    if (count($entries) > $this->resultsPerPage) {
      $last = array_pop($entries);
      $this->view->setVar('nextPage', $this->buildPaginationUrlForCouch($last->publishedAt, $last->id));
    }

    $this->view->setVar('entries', $entries);
    $this->view->setVar('entriesCount', Helper\Text::formatNumber($count));
    $this->view->setVar('resource', $tag);
    $this->view->setVar('title', sprintf('%s per data', ucfirst($this->getLabel())));
  }


  protected function popular($filter, $tagId = NULL) {
    $period = $this->getPeriod($filter);
    if ($period === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $date = Helper\Time::aWhileBack($period, "_");

    if ($this->isSameClass())
      $set = "pop_".$tagId."post".$date;
    else
      $set = "pop_".$tagId.$this->type.$date;

    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $keys = $this->redis->zRevRangeByScore($set, '+inf', 0, ['limit' => [$offset, $this->resultsPerPage-1]]);
    $count = $this->redis->zCount($set, 0, '+inf');

    if ($count > $this->resultsPerPage)
      $this->view->setVar('nextPage', $this->buildPaginationUrlForRedis($offset + $this->resultsPerPage));

    if (!empty($keys)) {
      $opts = new ViewQueryOpts();
      $opts->doNotReduce();
      $rows = $this->couch->queryView("posts", "unversion", $keys, $opts);
      $ids = $this->getEntries(array_column($rows->asArray(), 'id'));
    }
    else
      $ids = [];

    $this->view->setVar('entries', $ids);
    $this->view->setVar('entriesCount', Helper\Text::formatNumber($count));
    $this->view->setVar('submenu', $this->periods);
    $this->view->setVar('submenuIndex', $period);
    $this->view->setVar('title', sprintf('%s popolari', ucfirst($this->getLabel())));
  }


  /**
   * @brief Displays the most popular updates for the provided period (ordered by score).
   * @param[in] string $filter Human readable representation of a period.
   */
  public function popularAction($filter = NULL) {
    $this->popular($filter);
  }


  /**
   * @brief Displays the most popular updates by tag, for the provided period (ordered by score).
   * @param[in] string $tag The tag name.
   * @param[in] string $filter Human readable representation of a period.
   */
  public function popularByTagAction($tag, $filter = NULL) {
    $tagId = $this->getTagId($tag);
    if ($tagId === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $this->popular($filter, $tagId."_");
    $this->view->setVar('resource', $tag);
  }


  protected function active($tagId = NULL) {
    if ($this->isSameClass())
      $set = "tmp_".$tagId."post";
    else
      $set = "tmp_".$tagId.$this->type;

    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $keys = $this->redis->zRevRangeByScore($set, '+inf', 0, ['limit' => [$offset, $this->resultsPerPage-1]]);
    $count = $this->redis->zCount($set, 0, '+inf');

    if ($count > $this->resultsPerPage)
      $this->view->setVar('nextPage', $this->buildPaginationUrlForRedis($offset + $this->resultsPerPage));

    if (!empty($keys)) {
      $opts = new ViewQueryOpts();
      $opts->doNotReduce();
      $rows = $this->couch->queryView("posts", "unversion", $keys, $opts);
      $ids = $this->getEntries(array_column($rows->asArray(), 'id'));
    }
    else
      $ids = [];

    $this->view->setVar('entries', $ids);
    $this->view->setVar('entriesCount', Helper\Text::formatNumber($count));
    $this->view->setVar('title', sprintf('%s attivi', ucfirst($this->getLabel())));
  }


  /**
   * @brief Displays the last updated entries.
   */
  public function activeAction() {
    $this->active();
  }


  /**
   * @brief Displays the last updated entries by tag.
   */
  public function activeByTagAction($tag) {
    $tagId = $this->getTagId($tag);
    if ($tagId === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $this->active($tagId."_");
    $this->view->setVar('resource', $tag);
  }


  /**
   * @brief Displays the newest updates based on my tags.
   */
  public function interestingAction() {
    /*
    $opts = new ViewQueryOpts();
    $opts->reduce()->groupResults();
    //$opts->setLimit($this->resultsPerPage+1);

    // Paginates results.
    //$startKey = isset($_GET['startkey']) ? (int)$_GET['startkey'] : Couch::WildCard();
    //if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    if ($this->isSameClass()) {
      //$opts->setStartKey($startKey);

      $keys = ['3e96144b-3ebd-41e4-8a45-78cd9af1671d', '493e48ea-78f0-4a64-be17-6cacf21f848b'];

      //$keys[] = $tagId;
      $rows = $this->couch->queryView("posts", "byTag", $keys, $opts);
    }
    else {
      //$opts->setStartKey([$this->type, $startKey])->setEndKey([$this->type]);
      //$rows = $this->couch->queryView("posts", "perDateByType", NULL, $opts);
    }


    $this->monolog->addDebug('Posts: ', $rows->asArray());

    $phpCount = count($rows->asArray()[0]['value']);
    $mysqlCount = count($rows->asArray()[1]['value']);
    $this->monolog->addDebug(sprintf('php: %d', $phpCount));
    $this->monolog->addDebug(sprintf('mysql: %d', $mysqlCount));

    $this->monolog->addDebug(sprintf('total: %d', $phpCount + $mysqlCount));

    $merged = array_merge($rows->asArray()[0]['value'], $rows->asArray()[1]['value']);
    $this->monolog->addDebug(sprintf('total merge: %d', count($merged)));
    $this->monolog->addDebug(sprintf('total unique: %d', count(array_unique($merged))));



    $entries = $this->getEntries($rows->asArray()[1]['value']);

    $this->monolog->addDebug('Qui passo');

    if (count($entries) > $this->resultsPerPage) {
      $last = array_pop($entries);
      $this->view->setVar('nextPage', $this->buildPaginationUrl($last->publishedAt, $last->id));
    }

    $this->view->setVar('entries', $entries);
    //$this->view->setVar('entriesCount', $this->countPosts());
    */
    $this->view->setVar('entriesCount', 0);
    $this->view->setVar('title', sprintf('%s interessanti', ucfirst($this->getLabel())));
  }


  /**
   * @brief Displays the user favorites.
   */
  public function favoriteAction($filter = NULL) {
    // Stores sub-menu definition.
    $filters = ['data-pubblicazione' => 0, 'data-inserimento' => 1];
    if (is_null($filter)) $filter = 'data-inserimento';

    $index = Helper\ArrayHelper::value($filter, $filters);
    if ($index === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    if ($index == 0) {
      $perDate = 'perPublishedAt';
      $perDateByType = 'perPublishedAtByType';
    }
    else {
      $perDate = 'perAddedAt';
      $perDateByType = 'perAddedAtByType';
    }

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults()->setLimit($this->resultsPerPage+1);

    // Paginates results.
    $startKey = isset($_GET['startkey']) ? (int)$_GET['startkey'] : Couch::WildCard();
    if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    if ($this->isSameClass()) {
      $opts->setStartKey([$this->user->id, $startKey])->setEndKey([$this->user->id]);
      $rows = $this->couch->queryView("favorites", $perDate, NULL, $opts);

      $opts->reduce()->setStartKey([$this->user->id, Couch::WildCard()])->unsetOpt('startkey_docid');
      $count = $this->couch->queryView("favorites", $perDate, NULL, $opts)->getReducedValue();

      $key = 1;
    }
    else {
      $opts->setStartKey([$this->user->id, $this->type, $startKey])->setEndKey([$this->user->id, $this->type]);
      $rows = $this->couch->queryView("favorites", $perDateByType, NULL, $opts);

      $opts->reduce()->setStartKey([$this->user->id, $this->type, Couch::WildCard()])->unsetOpt('startkey_docid');
      $count = $this->couch->queryView("favorites", $perDateByType, NULL, $opts)->getReducedValue();

      $key = 2;
    }

    // We get the document IDs pruned by their version number, but we need them.
    $stars = $rows->asArray();

    // If the query returned more entries than the ones must display on the page, a link to the next page must be provided.
    if ($rows->count() > $this->resultsPerPage) {
      $last = array_pop($stars);
      $this->view->setVar('nextPage', $this->buildPaginationUrlForCouch($last['key'][$key], $last['id']));
    }

    // So we make another query to retrieves the IDs.
    if (empty($stars))
      $posts = [];
    else {
      $opts->reset();
      $opts->doNotReduce();
      $posts = $this->couch->queryView("posts", "unversion", array_column($stars, 'value'), $opts)->asArray();
    }

    $this->view->setVar('entries', $this->getEntries(array_column($posts, 'id')));
    $this->view->setVar('entriesCount', Helper\Text::formatNumber($count));
    $this->view->setVar('entriesLabel', 'preferiti');
    $this->view->setVar('submenu', $filters);
    $this->view->setVar('submenuIndex', $index);
    $this->view->setVar('title', sprintf('%s preferiti', ucfirst($this->getLabel())));
  }

}
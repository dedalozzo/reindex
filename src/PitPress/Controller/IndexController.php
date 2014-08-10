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
   * @brief Gets the total number of posts.
   */
  protected function countPosts() {
    if ($this->isSameClass()) {
      $count = $this->couch->queryView("posts", "perDate")->getReducedValue();
    }
    else {
      $opts = new ViewQueryOpts();
      $opts->setStartKey([$this->type])->setEndKey([$this->type, Couch::WildCard()]);
      $count = $this->couch->queryView('posts', 'perDateByType', NULL, $opts)->getReducedValue();
    }

    return Helper\Text::formatNumber($count);
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
    $keys = array_column($rows->asArray(), 'id');

    // Posts.
    $opts->reset();
    $opts->doNotReduce()->includeMissingKeys();
    $posts = $this->couch->queryView("posts", "all", $keys, $opts);

    // Scores.
    $opts->reset();
    $opts->includeMissingKeys()->groupResults();
    $scores = $this->couch->queryView("votes", "perPost", $keys, $opts);

    // Replies.
    $opts->reset();
    $opts->includeMissingKeys()->groupResults();
    $replies = $this->couch->queryView("replies", "perPost", $keys, $opts);

    $entries = [];
    $postCount = count($posts);
    for ($i = 0; $i < $postCount; $i++) {
      $entry = new \stdClass();
      $entry->id = $posts[$i]['id'];

      $properties = $posts[$i]['value'];
      $entry->title = $properties['title'];
      $entry->url = $this->buildPostUrl($properties['publishingDate'], $properties['slug']);
      $entry->whenHasBeenPublished = Helper\Time::when($properties['publishingDate']);
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
    // todo Change this part, getting the classification of the last week, grouped by tagId.
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit($count);
    // todo Change newest...
    $classifications = $this->couch->queryView("classifications", "newest", NULL, $opts);
    $keys = array_column($classifications->asArray(), 'value');

    $opts->reset();
    $tags = $this->couch->queryView("tags", "allNames", $keys, $opts);

    $opts->reset();
    $opts->includeMissingKeys()->groupResults();
    $postsPerTag = $this->couch->queryView("classifications", "perTag", $keys, $opts);

    $recentTags = [];
    for ($i = 0; $i < $count; $i++)
      $recentTags[] = [$tags[$i]['value'], $postsPerTag[$i]['value']];

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

    // Overrides the section name because index its own subclasses belong to the same section.
    $this->view->setVar('sectionName', 'home');

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
          'action' => 'signin'
        ]);
  }


  /**
   * @brief Displays the newest posts.
   */
  public function newestAction() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults()->setLimit(self::RESULTS_PER_PAGE+1);

    // Paginates results.
    $startKey = isset($_GET['startkey']) ? (int)$_GET['startkey'] : Couch::WildCard();
    if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    if ($this->isSameClass()) {
      $opts->setStartKey($startKey);
      $rows = $this->couch->queryView("posts", "perDate", NULL, $opts);
    }
    else {
      $opts->setStartKey([$this->type, $startKey])->setEndKey([$this->type]);
      $rows = $this->couch->queryView("posts", "perDateByType", NULL, $opts);
    }

    $entries = $this->getEntries(array_column($rows->asArray(), 'id'));

    if (count($entries) > self::RESULTS_PER_PAGE) {
      $last = array_pop($entries);
      $this->view->setVar('nextPage', $this->buildPaginationUrl($last->publishingDate, $last->id));
    }

    $this->view->setVar('entries', $entries);
    $this->view->setVar('entriesCount', $this->countPosts());

    if (is_null($this->view->title))
      $this->view->setVar('title', sprintf('Nuovi %s', $this->getLabel()));
  }


  /**
   * @brief Displays the posts per date.
   */
  public function perDateAction($year, $month = NULL, $day = NULL) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults()->setLimit(self::RESULTS_PER_PAGE+1);

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

    if (count($entries) > self::RESULTS_PER_PAGE) {
      $last = array_pop($entries);
      $this->view->setVar('nextPage', $this->buildPaginationUrl($last->publishingDate, $last->id));
    }

    $this->view->setVar('entries', $entries);
    $this->view->setVar('entriesCount', Helper\Text::formatNumber($count));
    $this->view->setVar('title', sprintf('%s per data', ucfirst($this->getLabel())));
  }


  /**
   * @brief Displays the most popular updates for the provided period.
   * @todo Replace `posts` with `scores`.
   */
  public function popularAction($filter = NULL) {
    $period = $this->getPeriod($filter);
    if ($period === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(self::RESULTS_PER_PAGE+1)->reverseOrderOfResults();

    if ($this->isSameClass()) {
      $opts->setStartKey(time());

      if ($period == Helper\Time::EVER)
        // ERROR: don't provide a data and use another view that use as key the score and as value the postId
        $opts->setEndKey(0);
      else
        $opts->setEndKey(Helper\Time::aWhileBack($period));

      $rows = $this->couch->queryView("posts", "perDate", NULL, $opts);
      $count = $this->couch->queryView("posts", "perDate", NULL, $opts->reduce())->getReducedValue();
    }
    else {
      $opts->setStartKey([$this->type, Couch::WildCard()]);

      if ($period == Helper\Time::EVER)
        // ERROR: don't provide a data and use another view that use as key the [type, score] and as value the postId
        $opts->setEndKey([$this->type]);
      else
        $opts->setEndKey([$this->type, Helper\Time::aWhileBack($period)]);

      $rows = $this->couch->queryView("posts", "perDateByType", NULL, $opts);
      $count = $this->couch->queryView("posts", "perDateByType", NULL, $opts->reduce())->getReducedValue();
    }

    $this->monolog->addNotice(sprintf('Period: %s', $period));

    $this->view->setVar('entries', $this->getEntries(array_column($rows->asArray(), 'id')));
    $this->view->setVar('entriesCount', $count);
    $this->view->setVar('submenu', $this->periods);
    $this->view->setVar('submenuIndex', $period);
    $this->view->setVar('title', sprintf('%s popolari', ucfirst($this->getLabel())));
  }


  /**
   * @brief Displays the last updated entries.
   */
  public function activeAction() {
    $this->view->setVar('entriesCount', 0);
    $this->view->setVar('title', sprintf('%s attivi', ucfirst($this->getLabel())));
  }


  /**
   * @brief Displays the newest updates based on my tags.
   */
  public function interestingAction() {
    $this->view->setVar('entriesCount', 0);
    $this->view->setVar('title', sprintf('%s interessanti', ucfirst($this->getLabel())));
  }


  public function favoriteAction() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults()->setLimit(self::RESULTS_PER_PAGE+1);

    // Paginates results.
    $startKey = isset($_GET['startkey']) ? (int)$_GET['startkey'] : Couch::WildCard();
    if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    if ($this->isSameClass()) {
      $opts->doNotReduce()->setLimit(self::RESULTS_PER_PAGE+1)->reverseOrderOfResults()->setStartKey([$this->user->id, $startKey])->setEndKey([$this->user->id]);
      $rows = $this->couch->queryView("favorites", "perDateAdded", NULL, $opts);

      $opts->reduce()->setStartKey([$this->user->id, Couch::WildCard()])->unsetOpt('startkey_docid');
      $count = $this->couch->queryView("favorites", "perDateAdded", NULL, $opts)->getReducedValue();

      $key = 1;
    }
    else {
      $opts->doNotReduce()->setLimit(self::RESULTS_PER_PAGE+1)->reverseOrderOfResults()->setStartKey([$this->user->id, $this->type, $startKey])->setEndKey([$this->user->id, $this->type]);
      $rows = $this->couch->queryView("favorites", "perDateAddedByType", NULL, $opts);

      $opts->reduce()->setStartKey([$this->user->id, $this->type, Couch::WildCard()])->unsetOpt('startkey_docid');
      $count = $this->couch->queryView("favorites", "perDateAddedByType", NULL, $opts)->getReducedValue();

      $key = 2;
    }

    $stars = $rows->asArray();
    // If the query returned more entries than the ones must display on the page, a link to the next page must be provided.
    if ($rows->count() > self::RESULTS_PER_PAGE) {
      $last = array_pop($stars);
      $this->view->setVar('nextPage', $this->buildPaginationUrl($last['key'][$key], $last['id']));
    }

    $this->view->setVar('entries', $this->getEntries(array_column($stars, 'value')));
    $this->view->setVar('entriesCount', Helper\Text::formatNumber($count));
    $this->view->setVar('entriesLabel', 'preferiti');
    $this->view->setVar('title', sprintf('%s preferiti', ucfirst($this->getLabel())));
  }


}
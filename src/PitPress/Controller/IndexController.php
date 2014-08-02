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
  protected function getInfo($viewName, $type, $count = 20) {
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
    for ($i = 0; $i < $postCount - 1; $i++) {
      $entry = new \stdClass();
      $entry->id = $posts[$i]['id'];

      $properties = $posts[$i]['value'];
      $entry->title = $properties['title'];
      $entry->url = $this->buildUrl($properties['publishingDate'], $properties['slug']);
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
  protected function recentTags() {
    // todo Change this part, getting the classification of the last week, grouped by tagId.
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(60);
    // todo Change newest...
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
    $this->view->setVar('books', $this->getInfo('perDateByType', 'link'));
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

    if ($this->isSameClass()) {
      $opts->doNotReduce()->reverseOrderOfResults()->setLimit(30);
      $rows = $this->couch->queryView("posts", "perDate", NULL, $opts);
    }
    else {
      $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey([$this->type, Couch::WildCard()])->setEndKey([$this->type]);
      $rows = $this->couch->queryView("posts", "perDateByType", NULL, $opts);
    }

    $this->view->setVar('entries', $this->getEntries(array_column($rows->asArray(), 'id')));
    $this->view->setVar('entriesCount', $this->countPosts());

    if (is_null($this->view->title))
      $this->view->setVar('title', sprintf('Nuovi %s', $this->getLabel()));
  }


  /**
   * @brief Displays the posts per date.
   */
  public function perDateAction($year, $month = NULL, $day = NULL) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults();

    $aDay = (is_null($day)) ? 1 : (int)$day;
    $aMonth = (is_null($month)) ? 1 : (int)$month;
    $aYear = (int)$year;

    $startDate = (new \DateTime())->setDate($aYear, $aMonth, $aDay)->modify('midnight');
    $endDate = clone($startDate);

    if (isset($day))
      $endDate->modify('tomorrow')->modify('last second');
    elseif (isset($month))
      $endDate->modify('last day of this month')->modify('last second');
    else
      $endDate->setDate($aYear, 12, 31)->modify('last second');

    //$this->monolog->addDebug(sprintf('startDate: %s', $startDate->format(\DateTime::ATOM)));
    //$this->monolog->addDebug(sprintf('endDate: %s', $endDate->format(\DateTime::ATOM)));

    if ($this->isSameClass()) {
      $opts->setStartKey($endDate->getTimestamp())->setEndKey($startDate->getTimestamp());
      $rows = $this->couch->queryView("posts", "perDate", NULL, $opts);
      $count = $this->couch->queryView("posts", "perDate", NULL, $opts->reduce())->getReducedValue();
    }
    else {
      $opts->setStartKey([$this->type, $endDate->getTimestamp()])->setEndKey([$this->type, $startDate->getTimestamp()]);
      $rows = $this->couch->queryView("posts", "perDateByType", NULL, $opts);
      $count = $this->couch->queryView("posts", "perDateByType", NULL, $opts->reduce())->getReducedValue();
    }

    $this->view->setVar('entries', $this->getEntries(array_column($rows->asArray(), 'id')));
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
    $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults();

    if ($this->isSameClass()) {
      $opts->setStartKey(time());

      if ($period == Helper\Time::EVER)
        $opts->setEndKey(0);
      else
        $opts->setEndKey(Helper\Time::aWhileBack($period));

      $rows = $this->couch->queryView("posts", "perDate", NULL, $opts);
      $count = $this->couch->queryView("posts", "perDate", NULL, $opts->reduce())->getReducedValue();
    }
    else {
      $opts->setStartKey([$this->type, Couch::WildCard()]);

      if ($period == Helper\Time::EVER)
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

}
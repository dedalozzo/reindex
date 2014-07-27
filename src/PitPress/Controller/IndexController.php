<?php

/**
 * @file IndexController.php
 * @brief This file contains the IndexController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Controller;


use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper\Time;

use Phalcon\Mvc\View;


/**
 * @brief Controller of Index actions.
 * @nosubgrouping
 */
class IndexController extends ListController {

  // Stores the still open answer sub-menu definition.
  protected static $stillOpenSubMenu = ['nessuna-risposta/', 'popolari/', 'nuove/', 'rivolte-a-me/'];


  /**
   * @brief Gets a list of tags recently updated.
   */
  protected function recentTags() {
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

    $this->view->setVar('recentTags', $recentTags);
  }


  /*
   * @brief Gets the posts info per type.
   */
  protected function getPostsInfoPerType($viewName, $type, $count = 20) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit($count)->reverseOrderOfResults()->setStartKey([$type, new \stdClass()])->setEndKey([$type]);
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
      $entry->whenHasBeenPublished = Time::when($properties['publishingDate']);
      $entry->score = is_null($scores[$i]['value']) ? 0 : $scores[$i]['value'];
      $entry->repliesCount = is_null($replies[$i]['value']) ? 0 : $replies[$i]['value'];

      $entries[] = $entry;
    }

    return $entries;
  }


  /*
   * @brief Given a filter name returns the correspondent type. In case the filter is invalid returns `false`, otherwise
   * the filter name or empty is no filter has been provided.
   * @param[in] string $filter The filter name.
   * @return string|bool|null
   */
  protected function getTypeByFilter($filter) {

    if (empty($filter))
      return NULL;

    switch ($filter) {
      case 'contributi':
        $type = NULL;
        break;
      case 'articoli':
        $type = 'article';
        break;
      case 'libri':
        $type = 'book';
        break;
      case 'links':
        $type = 'link';
        break;
      case 'domande':
        $type = 'question';
        break;
      default:
        return FALSE;
    }

    return $type;
  }


  protected function perDateByType($type, $year, $month, $day) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults();

    $aDay = (empty($day)) ? 1 : (int)$day;
    $aMonth = (empty($month)) ? 1 : (int)$month;
    $aYear = (int)$year;

    $startDate = (new \DateTime())->setDate($aYear, $aMonth, $aDay)->modify('midnight');
    $endDate = clone($startDate);

    if (!empty($day))
      $endDate->modify('tomorrow')->modify('last second');
    elseif (!empty($month))
      $endDate->modify('last day of this month')->modify('last second');
    else
      $endDate->setDate($aYear, 12, 31)->modify('last second');

    //$this->monolog->addNotice(sprintf('startDate: %s', $startDate->format(\DateTime::ATOM)));
    //$this->monolog->addNotice(sprintf('endDate: %s', $endDate->format(\DateTime::ATOM)));

    if (empty($type)) {
      $opts->setStartKey($endDate->getTimestamp())->setEndKey($startDate->getTimestamp());
      $rows = $this->couch->queryView("posts", "perDate", NULL, $opts);
      $postsCount = $this->couch->queryView("posts", "perDate", NULL, $opts->reduce());
    }
    else {
      $opts->setStartKey([$type, $endDate->getTimestamp()])->setEndKey([$type, $startDate->getTimestamp()]);
      $rows = $this->couch->queryView("posts", "perDateByType", NULL, $opts);
      $postsCount = $this->couch->queryView("posts", "perDate", NULL, $opts->reduce());
    }

    $this->view->setVar('entries', $this->getEntries(array_column($rows->asArray(), 'id')));
    $this->view->setVar('entriesCount', $postsCount);
  }


  protected function popularEver($type) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey([$type, time(), new \stdClass()])->setEndKey([$type, 0, 0]);

    $rows = $this->couch->queryView('scores', 'perType', NULL, $opts)->asArray();

    $this->view->setVar('entries', $this->getEntries(array_column($rows, 'value')));
  }


  /*
 * @brief Gets the popular posts per type in a period.
 */
  protected function popularInPeriod($type, $period) {
    $opts = new ViewQueryOpts();

    if ($period != 'sempre')
      $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey([$type, time()])->setEndKey([$type, Time::timestamp($period)]);
    else
      $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey([$type, new \stdClass()])->setEndKey([$type]);

    $rows = $this->couch->queryView('posts', 'perDateByType', NULL, $opts);

    $this->view->setVar('entries', $this->getEntries(array_column($rows->asArray(), 'id')));
  }



  public function initialize() {
    parent::initialize();

    $this->assets->addJs("/pit-bootstrap/dist/js/tab.min.js", FALSE);
    $this->assets->addJs("/pit-bootstrap/dist/js/pit.min.js", FALSE);
  }


  public function afterExecuteRoute() {
    parent::afterExecuteRoute();

    $this->recentTags();

    $this->view->setVar('questions', $this->getPostsInfoPerType('perDateByType', 'question'));
    $this->view->setVar('articles', $this->getPostsInfoPerType('perDateByType', 'article'));
    $this->view->setVar('books', $this->getPostsInfoPerType('perDateByType', 'book'));
  }


  /**
   * @brief Page index.
   */
  public function indexAction() {
    if (isset($this->user))
      return $this->dispatcher->forward(
        [
          'controller' => 'index',
          'action' => 'newest'
        ]);
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
  public function newestAction($filter = NULL) {
    $type = $this->getTypeByFilter($filter);

    if ($type === FALSE)
      return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);
    else
      $this->view->setVar('postType', $type);

    $opts = new ViewQueryOpts();

    if (is_null($type)) {
      $opts->doNotReduce()->reverseOrderOfResults()->setLimit(30);
      $rows = $this->couch->queryView("posts", "perDate", NULL, $opts);
      $this->view->setVar('filter', 'none');
      $this->view->setVar('entriesCount', $this->getPostsCount());
      $this->view->setVar('entriesLabel', 'contributi');
    }
    else {
      $opts->doNotReduce()->setLimit(30)->reverseOrderOfResults()->setStartKey([$type, new \stdClass()])->setEndKey([$type]);
      $rows = $this->couch->queryView("posts", "perDateByType", NULL, $opts);
      $this->view->setVar('entriesCount', $this->getPostsCountPerType($type));
      $this->view->setVar('entriesLabel', $filter);
    }

    $this->view->setVar('entries', $this->getEntries(array_column($rows->asArray(), 'id')));
  }


  /**
   * @brief Displays the posts per date.
   */
  public function perDateAction($year, $month = NULL, $day = NULL) {
    $this->perDateByType(NULL, $year, $month, $day);

    $this->view->setVar('title', 'Contributi per data');
  }


  /**
   * @brief Displays the posts per date by type.
   */
  public function perDateByTypeAction($filter, $year, $month = NULL, $day = NULL) {
    $type = $this->getTypeByFilter($filter);

    if ($type === FALSE)
      return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);
    else
      $this->view->setVar('postType', $type);

    $this->perDateByType($type, $year, $month, $day);

    $this->view->setVar('title', sprintf('%s per data', ucfirst($filter)));
  }


  /**
   * @brief Displays the most popular updates for the provided period.
   */
  public function popularAction($filter = NULL, $period = '24-ore') {
    $type = $this->getTypeByFilter($filter);

    if ($type === FALSE)
      return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);
    else
      $this->view->setVar('postType', $type);

    if (empty($period))
      $period = '24-ore';

    $periodIndex = Time::periodIndex($period);
    if ($periodIndex === FALSE)
      return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $this->view->setVar('subsectionMenu', Time::periods(6));
    $this->view->setVar('subsectionIndex', Time::periodIndex($period));

    $this->popularEver(new \stdClass(), $period);
  }


  /**
   * @brief Displays the last updated entries.
   */
  public function activeAction($filter = NULL) {
    $type = $this->getTypeByFilter($filter);

    if ($type === FALSE)
      return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);
    else
      $this->view->setVar('postType', $type);
  }


  /**
   * @brief Displays the newest updates based on my tags.
   */
  public function interestingAction($filter = NULL) {
    $type = $this->getTypeByFilter($filter);

    if ($type === FALSE)
      return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);
    else
      $this->view->setVar('postType', $type);
  }


  /**
   * @brief Displays the newest questions having a bounty.
   */
  public function importantAction() {
    $this->view->setVar('postType', 'question');
  }


  /**
   * @brief Displays the questions, still open, based on user's tags.
   */
  public function openAction($filter = NULL) {
    $this->view->setVar('postType', 'question');

    if (empty($filter))
      $filter = 'rivolte-a-me';

    $this->view->setVar('subsectionMenu', self::$stillOpenSubMenu);
    $this->view->setVar('subsectionIndex', array_flip(self::$stillOpenSubMenu)[$filter]);
  }

}
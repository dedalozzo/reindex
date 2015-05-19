<?php

/**
 * @file IndexController.php
 * @brief This file contains the IndexController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Controller;


use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use PitPress\Helper;
use PitPress\Exception\InvalidFieldException;
use PitPress\Model\Post;

use Phalcon\Mvc\View;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Tag;


/**
 * @brief Controller of Index actions.
 * @nosubgrouping
 */
class IndexController extends ListController {

  // Actions that aren't listing actions.
  protected static $actions = ['show', 'edit', 'new'];


  /**
   * @brief Returns a human readable label for the controller.
   * @retval string
   */
  protected function getLabel() {
    return 'contributi';
  }


  /**
   * @brief Returns `true` if the caller object is an instance of the class implementing this method, `false` otherwise.
   * @retval bool
   */
  protected function isSameClass() {
    return get_class($this) == get_class();
  }


  /**
   * @brief Given a tag's name, returns its id.
   * @param[in] string $name The tag's name.
   * @retval string|bool Returns the tag id, or `false` in case the tag doesn't exist.
   */
  protected function getTagId($name) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey($name);

    $rows = $this->couch->queryView('tags', 'byName', NULL, $opts);

    if ($rows->isEmpty())
      return FALSE;
    else
      return current($rows->getIterator())['id'];
  }


  /*
   * @brief Retrieves information for a bunch of posts.
   * @param[in] string $viewName The name of the view.
   * @param[in] string $type The type of posts.
   * @param[in] int $count The number of requested posts.
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
      $entry->url = Helper\Url::build($properties['publishedAt'], $properties['slug']);
      $entry->whenHasBeenPublished = Helper\Time::when($properties['publishedAt']);
      $entry->score = is_null($scores[$i]['value']) ? 0 : $scores[$i]['value'];
      $entry->repliesCount = is_null($replies[$i]['value']) ? 0 : $replies[$i]['value'];

      $entries[] = $entry;
    }

    return $entries;
  }


  /**
   * @brief Gets a list of tags recently updated.
   * @param[in] int $count The number of tags to be returned.
   */
  protected function recentTags($count = 20) {
    $recentTags = [];

    if ($this->isSameClass())
      $set = Post::UPD_SET.'tags'.'_'.'post';
    else
      $set = Post::UPD_SET.'tags'.'_'.$this->type;

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


  /**
   * @brief Adds CodeMirror Editor files.
   */
  protected function addCodeMirror() {
    $codeMirrorPath = "//cdnjs.cloudflare.com/ajax/libs/codemirror/".$this->di['config']['assets']['codeMirrorVersion'];
    $this->assets->addCss($codeMirrorPath."/codemirror.min.css", FALSE);
    $this->assets->addJs($codeMirrorPath."/codemirror.min.js", FALSE);
    $this->assets->addJs($codeMirrorPath."/addon/mode/overlay.min.js", FALSE);
    $this->assets->addJs($codeMirrorPath."/mode/xml/xml.min.js", FALSE);
    $this->assets->addJs($codeMirrorPath."/mode/markdown/markdown.min.js", FALSE);
    $this->assets->addJs($codeMirrorPath."/mode/gfm/gfm.min.js", FALSE);
    $this->assets->addJs($codeMirrorPath."/mode/javascript/javascript.min.js", FALSE);
    $this->assets->addJs($codeMirrorPath."/mode/css/css.min.js", FALSE);
    $this->assets->addJs($codeMirrorPath."/mode/htmlmixed/htmlmixed.min.js", FALSE);
    $this->assets->addJs($codeMirrorPath."/mode/clike/clike.min.js", FALSE);
  }


  /**
   * @brief Returns `true` when the called action is a listing action.
   * @retval bool
   */
  protected function isListing() {
    if (!in_array($this->actionName, static::$actions))
      return TRUE;
    else
      return FALSE;
  }


  public function initialize() {
    // Prevents to call the method twice in case of forwarding.
    if ($this->dispatcher->isFinished() && $this->dispatcher->wasForwarded())
      return;

    parent::initialize();

    if ($this->isListing()) {
      $this->type = $this->controllerName;
      $this->resultsPerPage = $this->di['config']->application->postsPerPage;

      $this->assets->addJs("/pit-bootstrap/dist/js/tab.min.js", FALSE);
      $this->assets->addJs("/pit-bootstrap/dist/js/list.min.js", FALSE);

      $this->view->pick('views/index');
    }
  }


  public function afterExecuteRoute() {
    // Prevents to call the method twice in case of forwarding.
    if ($this->dispatcher->isFinished() && $this->dispatcher->wasForwarded())
      return;

    parent::afterExecuteRoute();

    if ($this->isListing()) {
      $this->recentTags();

      // The entries label is printed below the entries count.
      $this->view->setVar('entriesLabel', $this->getLabel());

      // Those are the notebook pages, printed using the `updates.volt` widget.
      $this->view->setVar('questions', $this->getInfo('perDateByType', 'question'));
      $this->view->setVar('articles', $this->getInfo('perDateByType', 'article'));
      $this->view->setVar('books', $this->getInfo('perDateByType', 'book'));

      $this->log->addDebug(sprintf('Type: %s', $this->type));
    }

  }


  /**
   * @brief Page index.
   */
  public function indexAction() {
    if ($this->user->isMember()) {
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
   * @param[in] string $tag The tag name.
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
      //$rows = $this->couch->queryView("posts", "wegenerateanerror", NULL, $opts);

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
    $tagId = $this->getTagId($tag);
    if ($tagId === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $unversionTagId = Helper\Text::unversion($tagId);

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults()->setLimit($this->resultsPerPage+1);

    // Paginates results.
    $startKey = isset($_GET['startkey']) ? (int)$_GET['startkey'] : Couch::WildCard();
    if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    if ($this->isSameClass()) {
      $opts->setStartKey([$unversionTagId, $startKey])->setEndKey([$unversionTagId]);
      $rows = $this->couch->queryView("posts", "perDateByTag", NULL, $opts);

      $opts->reset();
      $opts->setStartKey([$unversionTagId])->setEndKey([$unversionTagId, Couch::WildCard()]);
      $count = $this->couch->queryView('posts', 'perDateByTag', NULL, $opts)->getReducedValue();
    }
    else {
      $opts->setStartKey([$unversionTagId, $this->type, $startKey])->setEndKey([$unversionTagId, $this->type]);
      $rows = $this->couch->queryView("posts", "perDateByTagAndType", NULL, $opts);

      $opts->reset();
      $opts->setStartKey([$unversionTagId, $this->type])->setEndKey([$unversionTagId, $this->type, Couch::WildCard()]);
      $count = $this->couch->queryView('posts', 'perDateByTagAndType', NULL, $opts)->getReducedValue();
    }

    $entries = $this->getEntries(array_column($rows->asArray(), 'id'));

    if (count($entries) > $this->resultsPerPage) {
      $last = array_pop($entries);
      $this->view->setVar('nextPage', $this->buildPaginationUrlForCouch($last->publishedAt, $last->id));
    }

    $this->view->setVar('entries', $entries);
    $this->view->setVar('entriesCount', Helper\Text::formatNumber($count));
    $this->view->setVar('etag', $this->couch->getDoc(Couch::STD_DOC_PATH, $tagId));

    if (is_null($this->view->title))
      $this->view->setVar('title', sprintf('Nuovi %s', $this->getLabel()));
  }


  /**
   * @brief Displays the posts per date.
   * @param[in] int $year An year.
   * @param[in] int $month (optional) A month.
   * @param[in] int $day (optional) A specific day.
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
   * @param[in] int $year An year.
   * @param[in] int $month (optional) A month.
   * @param[in] int $day (optional) A specific day.
   */
  public function perDateByTagAction($tag, $year, $month = NULL, $day = NULL) {
    $tagId = $this->getTagId($tag);
    if ($tagId === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $unversionTagId = Helper\Text::unversion($tagId);

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults()->setLimit($this->resultsPerPage+1);

    Helper\Time::dateLimits($minDate, $maxDate, $year, $month, $day);

    // Paginates results.
    $postDate = isset($_GET['startkey']) ? (new \DateTime())->setTimestamp((int)$_GET['startkey']) : clone($maxDate);
    if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    if ($this->isSameClass()) {
      $opts->setStartKey([$unversionTagId, $postDate->getTimestamp()])->setEndKey([$unversionTagId, $minDate->getTimestamp()]);
      $rows = $this->couch->queryView("posts", "perDateByTag", NULL, $opts);

      $opts->reduce()->setStartKey([$unversionTagId, $maxDate->getTimestamp()])->unsetOpt('startkey_docid');
      $count = $this->couch->queryView("posts", "perDateByTag", NULL, $opts)->getReducedValue();
    }
    else {
      $opts->setStartKey([$unversionTagId, $this->type, $postDate->getTimestamp()])->setEndKey([$unversionTagId, $this->type, $minDate->getTimestamp()]);
      $rows = $this->couch->queryView("posts", "perDateByTagAndType", NULL, $opts);

      $opts->reduce()->setStartKey([$unversionTagId, $this->type, $maxDate->getTimestamp()])->unsetOpt('startkey_docid');
      $count = $this->couch->queryView("posts", "perDateByTagAndType", NULL, $opts)->getReducedValue();
    }

    $entries = $this->getEntries(array_column($rows->asArray(), 'id'));

    if (count($entries) > $this->resultsPerPage) {
      $last = array_pop($entries);
      $this->view->setVar('nextPage', $this->buildPaginationUrlForCouch($last->publishedAt, $last->id));
    }

    $this->view->setVar('entries', $entries);
    $this->view->setVar('entriesCount', Helper\Text::formatNumber($count));
    $this->view->setVar('etag', $this->couch->getDoc(Couch::STD_DOC_PATH, $tagId));
    $this->view->setVar('title', sprintf('%s per data', ucfirst($this->getLabel())));
  }


  /**
   * @brief Used by popularAction() and popularByTagAction().
   * @param[in] string $filter Human readable representation of a period.
   * @param[in] string $unversionTagId (optional) An optional unversioned tag ID
   */
  protected function popular($filter, $unversionTagId = NULL) {
    $period = $this->getPeriod($filter);
    if ($period === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $date = Helper\Time::aWhileBack($period, "_");

    if ($this->isSameClass())
      $set = Post::POP_SET.$unversionTagId."post".$date;
    else
      $set = Post::POP_SET.$unversionTagId.$this->type.$date;

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
   * @param[in] string $filter (optional) Human readable representation of a period.
   */
  public function popularAction($filter = NULL) {
    $this->popular($filter);
  }


  /**
   * @brief Displays the most popular updates by tag, for the provided period (ordered by score).
   * @param[in] string $tag The tag name.
   * @param[in] string $filter (optional) Human readable representation of a period.
   */
  public function popularByTagAction($tag, $filter = NULL) {
    $tagId = $this->getTagId($tag);
    if ($tagId === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $unversionTagId = Helper\Text::unversion($tagId);

    $this->popular($filter, $unversionTagId."_");
    $this->view->setVar('etag', $this->couch->getDoc(Couch::STD_DOC_PATH, $tagId));
  }


  /**
   * @brief Used by activeAction() and activeByTagAction().
   * @param[in] string $unversionTagId (optional) An optional unversioned tag ID
   */
  protected function active($unversionTagId = NULL) {
    if ($this->isSameClass())
      $set = Post::UPD_SET.$unversionTagId."post";
    else
      $set = Post::UPD_SET.$unversionTagId.$this->type;

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
   * @param[in] string $tag The tag name.
   */
  public function activeByTagAction($tag) {
    $tagId = $this->getTagId($tag);
    if ($tagId === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $this->active(Helper\Text::unversion($tagId)."_");
    $this->view->setVar('etag', $this->couch->getDoc(Couch::STD_DOC_PATH, $tagId));
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


    $this->log->addDebug('Posts: ', $rows->asArray());

    $phpCount = count($rows->asArray()[0]['value']);
    $mysqlCount = count($rows->asArray()[1]['value']);
    $this->log->addDebug(sprintf('php: %d', $phpCount));
    $this->log->addDebug(sprintf('mysql: %d', $mysqlCount));

    $this->log->addDebug(sprintf('total: %d', $phpCount + $mysqlCount));

    $merged = array_merge($rows->asArray()[0]['value'], $rows->asArray()[1]['value']);
    $this->log->addDebug(sprintf('total merge: %d', count($merged)));
    $this->log->addDebug(sprintf('total unique: %d', count(array_unique($merged))));



    $entries = $this->getEntries($rows->asArray()[1]['value']);

    $this->log->addDebug('Qui passo');

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
   * @param[in] string $filter (optional) Human readable representation of a choice.
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


  /**
   * @brief Displays the post.
   * @todo Before to send a 404, we have check if does a post exist for the provided url, because maybe it's an old
   * revision of the same posts. Use the posts/approvedRevisionsByUrl view to check the existence, then make another
   * query on the posts/unversion to get the postId, and finally use it to get the document.
   * @param[in] int $year The year when a post has been published.
   * @param[in] int $month The month when a post has been published.
   * @param[in] int $day The exact day when a post has been published.
   * @param[in] string $slug The post' slug.
   */
  public function showAction($year, $month, $day, $slug) {
    $opts = new ViewQueryOpts();
    $opts->setKey([$year, $month, $day, $slug])->setLimit(1);
    $rows = $this->couch->queryView("posts", "byUrl", NULL, $opts);

    if ($rows->isEmpty())
      return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $post = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $rows[0]['id']);
    $post->incHits($post->creatorId);
    //$post->html = $this->markdown->parse($post->body);

    $this->view->setVar('post', $post);
    $this->view->setVar('replies', $post->getReplies());
    $this->view->setVar('title', $post->title);

    $this->assets->addJs("/pit-bootstrap/dist/js/post.min.js", FALSE);

    $this->view->pick('views/post/show');
  }


  /**
   * @brief Edits the post.
   * @param[in] string $id The post ID.
   */
  public function editAction($id) {
    if (empty($id))
      return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    if (is_null($this->user))
      return $this->dispatcher->forward(['controller' => 'auth', 'action' => 'signin']);

    // The validation object must be created in any case.
    $validation = new Helper\ValidationHelper();
    $this->view->setVar('validation', $validation);

    if ($this->request->isPost()) {

      try {
        $validation->setFilters("title", "trim");
        $validation->add("title", new PresenceOf(["message" => "Il titolo è obbligatorio."]));

        $validation->setFilters("body", "trim");
        $validation->add("body", new PresenceOf(["message" => "Il corpo è obbligatorio."]));

        $group = $validation->validate($_POST);
        if (count($group) > 0) {
          throw new InvalidFieldException("I campi sono incompleti o i valori indicati non sono validi. Gli errori sono segnalati in rosso sotto ai rispettivi campi d'inserimento.");
        }

        // Filters only the messages generated for the field 'name'.
        /*foreach ($validation->getMessages()->filter('email') as $message) {
          $this->flash->notice($message->getMessage());
          break;
        }*/

        $title = $this->request->getPost('email');
        $body = $this->request->getPost('body');
      }
      catch (\Exception $e) {
        // Displays the error message.
        $this->flash->error($e->getMessage());
      }

    }
    else {
      $post = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $id);

      if (!$post->canBeEdited())
        return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

      $opts = new ViewQueryOpts();
      $opts->setKey($post->unversionId)->doNotReduce();
      $revisions = $this->couch->queryView("revisions", "perItem", NULL, $opts);

      $keys = array_column(array_column($revisions->asArray(), 'value'), 'editorId');
      $opts->reset();
      $opts->includeMissingKeys();
      $users = $this->couch->queryView("users", "allNames", $keys, $opts);

      $versions = [];
      $revisionCount = count($revisions);
      for ($i = 0; $i < $revisionCount; $i++) {
        $version = (object)($revisions[$i]['value']);
        $version->id = $revisions[$i]['id'];
        $version->whenHasBeenModified = Helper\Time::when($version->modifiedAt);
        $version->editor = $users[$i]['value'][0];

        $versions[$version->modifiedAt] = $version;
      }

      krsort($versions);

      $this->tag->setDefault("title", $post->title);
      $this->tag->setDefault("body", $post->body);
    }

    $this->view->setVar('post', $post);
    $this->view->setVar('revisions', $versions);
    $this->view->setVar('title', $post->title);

    $this->view->disableLevel(View::LEVEL_LAYOUT);

    // Adds Selectize Plugin files.
    $this->assets->addJs("/pit-bootstrap/dist/js/selectize.min.js", FALSE);
    $this->addCodeMirror();

    $this->view->pick('views/post/edit');
  }


  /**
   * @brief Creates a new post.
   */
  public function newAction() {

  }

}
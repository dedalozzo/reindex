<?php

/**
 * @file TagController.php
 * @brief This file contains the TagController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Controller;


use EoC\Opt\ViewQueryOpts;
use EoC\Couch;

use ReIndex\Helper;
use ReIndex\Model\Tag;


/**
 * @brief Controller of Tag actions.
 * @nosubgrouping
 */
class TagController extends ListController {


  public function initialize() {
    // Prevents to call the method twice in case of forwarding.
    if ($this->dispatcher->isFinished() && $this->dispatcher->wasForwarded())
      return;

    parent::initialize();

    $this->resultsPerPage = $this->di['config']->application->tagsPerPage;
    $this->view->pick('views/tag');
  }


  public function afterExecuteRoute() {
    // Prevents to call the method twice in case of forwarding.
    if ($this->dispatcher->isFinished() && $this->dispatcher->wasForwarded())
      return;

    parent::afterExecuteRoute();
  }


  /**
   * @brief Displays the most popular tags.
   */
  public function popularAction() {
    $this->view->setVar('title', 'Tags popolari');
  }


  /**
   * @brief Displays the last updated tags.
   */
  public function activeAction() {
    $set = "tmp_tags".'_'.'post';

    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $keys = $this->redis->zRevRangeByScore($set, '+inf', 0, ['limit' => [$offset, $this->resultsPerPage-1]]);
    $count = $this->redis->zCount($set, 0, '+inf');

    if ($count > $this->resultsPerPage)
      $this->view->setVar('nextPage', $this->buildPaginationUrlForRedis($offset + $this->resultsPerPage));

    if (!empty($keys)) {
      $opts = new ViewQueryOpts();
      $opts->doNotReduce();
      $rows = $this->couch->queryView("tags", "allNames", $keys, $opts);
      $tags = Tag::collect(array_column($rows->asArray(), 'id'));
    }
    else
      $tags = [];

    $this->view->setVar('entries', $tags);
    $this->view->setVar('title', 'Tags attivi');
  }


  /**
   * @brief Displays the tags sorted by name.
   */
  public function byNameAction() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit($this->resultsPerPage+1);

    // Paginates results.
    $startKey = isset($_GET['startkey']) ? $_GET['startkey'] : chr(0);
    $opts->setStartKey($startKey);
    if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    $rows = $this->couch->queryView("tags", "byName", NULL, $opts)->asArray();

    $tags = Tag::collect(array_column($rows, 'id'));

    if (count($tags) > $this->resultsPerPage) {
      $last = array_pop($tags);
      $this->view->setVar('nextPage', $this->buildPaginationUrlForCouch($last->name, $last->id));
    }

    $this->view->setVar('entries', $tags);
    $this->view->setVar('title', 'Tags per nome');
  }


  /**
   * @brief Displays the newest tags.
   */
  public function newestAction() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults()->setLimit($this->resultsPerPage+1);

    // Paginates results.
    $startKey = isset($_GET['startkey']) ? (int)$_GET['startkey'] : Couch::WildCard();
    $opts->setStartKey($startKey);
    if (isset($_GET['startkey_docid'])) $opts->setStartDocId($_GET['startkey_docid']);

    $rows = $this->couch->queryView("tags", "newest", NULL, $opts)->asArray();

    $tags = Tag::collect(array_column($rows, 'id'));

    if (count($tags) > $this->resultsPerPage) {
      $last = array_pop($tags);
      $this->view->setVar('nextPage', $this->buildPaginationUrlForCouch($last->createdAt, $last->id));
    }

    $this->view->setVar('entries', $tags);
    $this->view->setVar('title', 'Nuovi tags');
  }


  /**
   * @brief Displays the synonyms.
   * @todo I still don't know how to make this one.
   */
  public function synonymsAction() {
    $this->view->setVar('title', 'Sinonimi');
  }


  /**
   * @brief Given a partial name, loads the list of tags matching it.
   * @retval string A JSON encoded object.
   */
  public function filterAction() {
    if ($this->request->hasPost('filter')) {
      $opts = new ViewQueryOpts();
      $opts->setKey($this->request->getPost('filter'));
      $rows = $this->couch->queryView('tags', 'substrings', NULL, $opts)->asArray();

      $tags = Tag::collect(array_column($rows, 'id'));
      echo json_encode($tags);

      $this->view->disable();
    }
    else {
      throw new \RuntimeException("Non hai specificato una query per la ricerca.");
    }
  }


  /**
   * @brief Edits the tag.
   * @param[in] string $id The tag ID.
   */
  public function editAction($id) {
  }


  /**
   * @brief Creates a new tag.
   */
  public function newAction() {
  }

}
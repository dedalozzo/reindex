<?php

/**
 * @file IndexController.php
 * @brief This file contains the IndexController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Controller;


use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use ReIndex\Security\Role;
use ReIndex\Validation;
use ReIndex\Helper;
use ReIndex\Exception\InvalidFieldException;
use ReIndex\Model\Post;

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

  // The controller name and also the document's type.
  protected $type;

  // Periods of time.
  protected $periods;


  /**
   * @brief Returns a human readable label for the controller.
   * @retval string
   */
  protected function getLabel() {
    return 'posts';
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
    $name = urldecode($name);

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
      $set = Post::ACT_SET.'tags'.'_'.'post';
    else
      $set = Post::ACT_SET.'tags'.'_'.$this->type;

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


  /**
   * @brief Retrieves all post IDs in a set between min and max.
   * @param[in] string $prefix Prefix of the Redis set.
   * @param[in] string $postfix Postfix of the Redis set.
   * @param[in] string $unversionTagId (optional) An optional unversioned tag ID.
   * @param[in] mixed $min Minimum score.
   * @param[in] mixed $max Maximum score.
   */
  protected function zRevRangeByScore($prefix, $postfix = '', $unversionTagId = NULL, $min = 0, $max = '+inf') {
    $subset = is_null($unversionTagId) ? '' : $unversionTagId . '_';

    if ($this->isSameClass())
      $set = $prefix . $subset . "post" . $postfix;
    else
      $set = $prefix . $subset . $this->type . $postfix;

    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $keys = $this->redis->zRevRangeByScore($set, $max, $min, ['limit' => [$offset, $this->resultsPerPage-1]]);
    $count = $this->redis->zCount($set, $min, $max);

    $nextOffset = $offset + $this->resultsPerPage;

    if ($count > $nextOffset)
      $this->view->setVar('nextPage', $this->buildPaginationUrlForRedis($nextOffset));

    if (!empty($keys)) {
      $opts = new ViewQueryOpts();
      $opts->doNotReduce();
      $rows = $this->couch->queryView("posts", "unversion", $keys, $opts);
      $posts = Post::collect(array_column($rows->asArray(), 'id'));
    }
    else
      $posts = [];

    $this->view->setVar('entries', $posts);
    $this->view->setVar('entriesCount', Helper\Text::formatNumber($count));
  }


  /**
   * @brief Used by perDateAction() and perDateByTagAction().
   * @param[in] \DateTime $minDate Minimum date.
   * @param[in] \DateTime $maxDate Maximum date.
   * @param[in] string $unversionTagId (optional) An optional unversioned tag ID.
   */
  protected function perDate(\DateTime $minDate, \DateTime $maxDate, $unversionTagId = NULL) {
    $this->zRevRangeByScore(Post::NEW_SET, '', $unversionTagId, $minDate->getTimestamp(), $maxDate->getTimestamp());

    $this->view->setVar('title', sprintf('%s by date', ucfirst($this->getLabel())));
  }


  /**
   * @brief Used by newestAction() and newestByTagAction().
   * @param[in] string $unversionTagId (optional) An optional unversioned tag ID.
   */
  protected function newest($unversionTagId = NULL) {
    $this->zRevRangeByScore(Post::NEW_SET, '', $unversionTagId);

    if (is_null($this->view->title))
      $this->view->setVar('title', sprintf('New %s', $this->getLabel()));
  }


  /**
   * @brief Used by popularAction() and popularByTagAction().
   * @param[in] string $filter A human readable period of time.
   * @param[in] string $unversionTagId (optional) An optional unversioned tag ID.
   */
  protected function popular($filter, $unversionTagId = NULL) {
    $filter = Helper\Time::period($filter);
    if ($filter === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $this->dispatcher->setParam('filter', $filter);

    $postfix = Helper\Time::aWhileBack($filter, "_");

    $this->zRevRangeByScore(Post::POP_SET, $postfix, $unversionTagId);

    $this->view->setVar('filters', $this->periods);
    $this->view->setVar('title', sprintf('Popular %s', ucfirst($this->getLabel())));
  }


  /**
   * @brief Used by activeAction() and activeByTagAction().
   * @param[in] string $unversionTagId (optional) An optional unversioned tag ID.
   */
  protected function active($unversionTagId = NULL) {
    $this->zRevRangeByScore(Post::ACT_SET, $unversionTagId);

    $this->view->setVar('title', sprintf('Active %s', ucfirst($this->getLabel())));
  }


  public function initialize() {
    // Prevents to call the method twice in case of forwarding.
    if ($this->dispatcher->isFinished() && $this->dispatcher->wasForwarded())
      return;

    parent::initialize();

    if ($this->isListing()) {
      $this->type = $this->controllerName;
      $this->resultsPerPage = $this->di['config']->application->postsPerPage;
      $this->periods = Helper\Time::$periods;

      $this->assets->addJs($this->dist."/js/tab.min.js", FALSE);
      $this->assets->addJs($this->dist."/js/list.min.js", FALSE);

      // FOR DEBUG PURPOSE ONLY UNCOMMENT THE FOLLOWING LINE AND COMMENT THE ONE ABOVE.
      //$this->assets->addJs("/reindex/themes/".$this->themeName."/src/js/list.js", FALSE);

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
      //$this->view->setVar('questions', $this->getInfo('perDateByType', 'question'));
      //$this->view->setVar('articles', $this->getInfo('perDateByType', 'article'));
      //$this->view->setVar('books', $this->getInfo('perDateByType', 'book'));

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
    $this->view->setVar('title', 'Popular tags');
  }


  /**
   * @brief Displays the posts per date.
   * @param[in] int $year An year.
   * @param[in] int $month (optional) A month.
   * @param[in] int $day (optional) A specific day.
   */
  public function perDateAction($year, $month = NULL, $day = NULL) {
    Helper\Time::dateLimits($minDate, $maxDate, $year, $month, $day);

    $this->perDate($minDate, $maxDate);
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

    Helper\Time::dateLimits($minDate, $maxDate, $year, $month, $day);

    $this->perDate($minDate, $maxDate, Helper\Text::unversion($tagId));

    $this->view->setVar('etag', $this->couch->getDoc(Couch::STD_DOC_PATH, $tagId));
  }


  /**
   * @brief Displays the newest posts.
   */
  public function newestAction() {
    $this->newest();
  }


  /**
   * @brief Displays the newest posts by tag.
   * @param[in] string $tag The tag name.
   */
  public function newestByTagAction($tag) {
    $tagId = $this->getTagId($tag);
    if ($tagId === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $this->newest(Helper\Text::unversion($tagId));

    $this->view->setVar('etag', $this->couch->getDoc(Couch::STD_DOC_PATH, $tagId));
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

    $this->popular($filter, Helper\Text::unversion($tagId));

    $this->view->setVar('etag', $this->couch->getDoc(Couch::STD_DOC_PATH, $tagId));
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

    $this->active(Helper\Text::unversion($tagId));

    $this->view->setVar('etag', $this->couch->getDoc(Couch::STD_DOC_PATH, $tagId));
  }


  /**
   * @brief Displays the newest updates based on my tags.
   */
  public function interestingAction() {
    $this->view->setVar('title', sprintf('%s associated with your favorite tags', ucfirst($this->getLabel())));
  }


  /**
   * @brief Displays the user favorites.
   * @param[in] string $filter (optional) Human readable representation of a choice.
   */
  public function favoriteAction($filter = 'insertion-date') {
    $filters = ['insertion-date' => NULL, 'posting-date' => NULL];

    $filter = Helper\ArrayHelper::key($filter, $filters);
    if ($filter === FALSE) return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    $this->dispatcher->setParam('filter', $filter);

    if ($filter == 'posting-date') {
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

    $userId = $this->user->getId();

    if ($this->isSameClass()) {
      $opts->setStartKey([$userId, $startKey])->setEndKey([$userId]);
      $rows = $this->couch->queryView("favorites", $perDate, NULL, $opts);

      $opts->reduce()->setStartKey([$userId, Couch::WildCard()])->unsetOpt('startkey_docid');
      $count = $this->couch->queryView("favorites", $perDate, NULL, $opts)->getReducedValue();

      $key = 1;
    }
    else {
      $opts->setStartKey([$userId, $this->type, $startKey])->setEndKey([$userId, $this->type]);
      $rows = $this->couch->queryView("favorites", $perDateByType, NULL, $opts);

      $opts->reduce()->setStartKey([$userId, $this->type, Couch::WildCard()])->unsetOpt('startkey_docid');
      $count = $this->couch->queryView("favorites", $perDateByType, NULL, $opts)->getReducedValue();

      $key = 2;
    }

    // We get the document IDs pruned by their version number, but we need them.
    $stars = $rows->asArray();

    // If the query returned more entries than the ones must display on the page, a link to the next page must be provided.
    if (count($rows) > $this->resultsPerPage) {
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

    $this->view->setVar('entries', Post::collect(array_column($posts, 'id')));
    $this->view->setVar('entriesCount', Helper\Text::formatNumber($count));
    $this->view->setVar('filters', $filters);
    $this->view->setVar('title', sprintf('Favorite %s', ucfirst($this->getLabel())));
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
    
    if (!$this->user->has(new Role\GuestRole\ViewPostPermission($post)))
      return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

    $post->incHits($post->creatorId);
    //$post->html = $this->markdown->parse($post->body);

    $this->view->setVar('post', $post);
    $this->view->setVar('canEdit', $this->user->has(new Role\MemberRole\EditPostPermission($post)));
    $this->view->setVar('replies', $post->getReplies());
    $this->view->setVar('title', $post->title);

    //$this->assets->addJs($this->dist."/js/post.min.js", FALSE);
    // FOR DEBUG PURPOSE ONLY UNCOMMENT THE FOLLOWING LINE AND COMMENT THE ONE ABOVE.
    $this->assets->addJs("/reindex/themes/".$this->themeName."/src/js/post.js", FALSE);

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
    $validation = new Validation();
    $this->view->setVar('validation', $validation);

    if ($this->request->isPost()) {

      try {
        $validation->setFilters("title", "trim");
        $validation->add("title", new PresenceOf(["message" => "Title is mandatory."]));

        $validation->setFilters("body", "trim");
        $validation->add("body", new PresenceOf(["message" => "Body is mandatory."]));

        $group = $validation->validate($_POST);
        if (count($group) > 0) {
          throw new InvalidFieldException("Fields are incomplete or the entered values are invalid. The errors are reported in red under the respective entry fields.");
        }

        // Filters only the messages generated for the field 'name'.
        /*foreach ($validation->getMessages()->filter('email') as $message) {
          $this->flash->notice($message->getMessage());
          break;
        }*/

        $title = $this->request->getPost('email');
        $body = $this->request->getPost('body');

        //$article->html = $this->markdown->parse($this->body);
        //$article->excerpt = Helper\Text::truncate(Helper\Text::purge($this->html));
      }
      catch (\Exception $e) {
        // Displays the error message.
        $this->flash->error($e->getMessage());
      }

    }
    else {
      $post = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $id);

      if (!$this->user->has(new Role\MemberRole\EditPostPermission($post)))
        return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show401']);

      $opts = new ViewQueryOpts();
      $opts->setKey($post->unversionId)->doNotReduce();
      $revisions = $this->couch->queryView("revisions", "perItem", NULL, $opts);

      $keys = array_column(array_column($revisions->asArray(), 'value'), 'editorId');
      $opts->reset();
      $opts->includeMissingKeys();
      $members = $this->couch->queryView("members", "allNames", $keys, $opts);

      $versions = [];
      $revisionCount = count($revisions);
      for ($i = 0; $i < $revisionCount; $i++) {
        $version = (object)($revisions[$i]['value']);
        $version->id = $revisions[$i]['id'];
        $version->whenHasBeenModified = Helper\Time::when($version->modifiedAt);
        $version->editor = $members[$i]['value'][0];

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
    $this->assets->addJs($this->dist."/js/selectize.min.js", FALSE);
    $this->addCodeMirror();

    $this->view->pick('views/post/edit');
  }


  /**
   * @brief Creates a new post.
   */
  public function newAction() {

  }

}
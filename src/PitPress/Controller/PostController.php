<?php

/**
 * @file PostController.php
 * @brief This file contains the PostController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Controller;


use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;

use Phalcon\Mvc\View;
use Phalcon\Validation\Validator\PresenceOf;

use PitPress\Exception\InvalidFieldException;
use PitPress\Helper\Text;
use PitPress\Helper\ValidationHelper;
use PitPress\Helper\Time;

use PitPress\Model\Tag;
use PitPress\Model\Link;
use PitPress\Model\Question;
use PitPress\Model\Book;
use PitPress\Model\Article;


/**
 * @brief Controller of Post actions.
 * @nosubgrouping
 */
class PostController extends BaseController {


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
   * @brief Displays the post.
   */
  public function showAction($year, $month, $day, $slug) {
    $opts = new ViewQueryOpts();
    $opts->setKey([$year, $month, $day, $slug])->setLimit(1);
    $rows = $this->couch->queryView("posts", "byUrl", NULL, $opts);

    if ($rows->isEmpty())
      return $this->dispatcher->forward(
        [
          'controller' => 'error',
          'action' => 'show404'
        ]);

    $post = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $rows[0]['id']);
    $post->incHits();
    $post->html = $this->markdown->parse($post->body);

    $this->view->setVar('post', $post);
    $this->view->setVar('replies', $post->getReplies());
    $this->view->setVar('title', $post->title);

    $this->assets->addJs("/pit-bootstrap/dist/js/post.min.js", FALSE);

    $this->view->pick('views/post/show');
  }


  /**
   * @brief Edits the post.
   */
  public function editAction($id) {
    if (empty($id))
      return $this->dispatcher->forward(['controller' => 'error', 'action' => 'show404']);

    if (is_null($this->user))
      return $this->dispatcher->forward(['controller' => 'auth', 'action' => 'signin']);

    // The validation object must be created in any case.
    $validation = new ValidationHelper();
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

      $opts = new ViewQueryOpts();
      $opts->setKey($post->unversionId)->doNotReduce();
      $revisions = $this->couch->queryView("revisions", "perPost", NULL, $opts);

      $keys = array_column(array_column($revisions->asArray(), 'value'), 'editorId');
      $opts->reset();
      $opts->includeMissingKeys();
      $users = $this->couch->queryView("users", "allNames", $keys, $opts);

      $versions = [];
      $revisionCount = count($revisions);
      for ($i = 0; $i < $revisionCount; $i++) {
        $version = (object)($revisions[$i]['value']);
        $version->id = $revisions[$i]['id'];
        $version->whenHasBeenModified = Time::when($version->modifiedAt);
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
   * @brief Creates a new link.
   */
  public function newLinkAction() {

  }


  /**
   * @brief Creates a new question.
   */
  public function newQuestionAction() {

  }


  /**
   * @brief Creates a new article.
   */
  public function newArticleAction() {
    if (is_null($this->user))
      return $this->dispatcher->forward(['controller' => 'auth', 'action' => 'signin']);

    // The validation object must be created in any case.
    $validation = new ValidationHelper();
    $this->view->setVar('validation', $validation);

    if ($this->request->isPost()) {

      try {
        $validation->setFilters("title", "trim");
        $validation->add("title", new PresenceOf(["message" => "Il titolo è obbligatorio."]));

        $validation->setFilters("body", "trim");
        $validation->add("body", new PresenceOf(["message" => "Il corpo è obbligatorio."]));

        $validation->add("tags", new PresenceOf(["message" => "Devi inserire almeno un tag."]));

        $group = $validation->validate($_POST);
        if (count($group) > 0) {
          throw new InvalidFieldException("I campi sono incompleti o i valori indicati non sono validi. Gli errori sono segnalati in rosso sotto ai rispettivi campi d'inserimento.");
        }

        $article = Article::create();
        $article->title = $this->request->getPost('title');
        $article->body = $this->request->getPost('body');
        $article->userId = $this->user->id;
        $article->addMultipleTagsAtOnce($this->request->getPost('tags'));

        $article->approve();
        $article->save();
      }
      catch (\Exception $e) {
        // Displays the error message.
        $this->flash->error($e->getMessage());
      }

    }
    else
      $this->setReferrer();

    //$this->view->setVar('post', $post);
    $this->view->setVar('title', 'Nuovo articolo');

    $this->view->disableLevel(View::LEVEL_LAYOUT);

    // Adds Selectize Plugin files.
    $this->assets->addJs("/pit-bootstrap/dist/js/selectize.min.js", FALSE);
    $this->addCodeMirror();

    $this->view->pick('views/post/new');
  }


  /**
   * @brief Creates a new book review.
   */
  public function newBookAction() {

  }

}
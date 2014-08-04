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


/**
 * @brief Controller of Post actions.
 * @nosubgrouping
 */
class PostController extends BaseController {


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
   * @brief Edit the post.
   */
  public function editAction($id) {
    if (empty($id))
      return $this->dispatcher->forward(
        [
          'controller' => 'error',
          'action' => 'show404'
        ]);

    $post = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $id);

    $this->tag->setDefault("title", $post->title);
    $this->tag->setDefault("body", $post->body);

    $this->view->setVar('post', $post);
    $this->view->setVar('title', $post->title);

    $this->view->disableLevel(View::LEVEL_LAYOUT);

    // Adds Selectize Plugin files.
    $this->assets->addJs("/pit-bootstrap/dist/js/selectize.min.js", FALSE);

    // Adds CodeMirror Editor files.
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

    $this->view->pick('views/post/edit');
  }

}
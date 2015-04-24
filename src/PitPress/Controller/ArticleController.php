<?php

//! @file ArticleController.php
//! @brief This file contains the ArticleController class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use PitPress\Helper;
use PitPress\Exception\InvalidFieldException;
use PitPress\Model\Article;

use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Mvc\View;


/**
 * @brief Controller of Article actions.
 * @nosubgrouping
 */
class ArticleController extends IndexController {


  protected function getLabel() {
    return 'articoli';
  }


  protected function getPeriod($filter) {
    return empty($filter) ? Helper\Time::EVER : Helper\ArrayHelper::value($filter, $this->periods);
  }


  protected function popular($filter, $unversionTagId = NULL) {
    $this->periods = Helper\ArrayHelper::slice($this->periods, 5);
    parent::popular($filter);
  }


  /**
   * @brief Creates a new article.
   */
  public function newAction() {
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

        $validation->add("tags", new PresenceOf(["message" => "Devi inserire almeno un tag."]));

        $group = $validation->validate($_POST);
        if (count($group) > 0) {
          throw new InvalidFieldException("I campi sono incompleti o i valori indicati non sono validi. Gli errori sono segnalati in rosso sotto ai rispettivi campi d'inserimento.");
        }

        $article = Article::create();
        $article->title = $this->request->getPost('title');
        $article->body = $this->request->getPost('body');
        $article->creatorId = $this->user->id;
        $article->addMultipleTagsAtOnce($this->request->getPost('tags'));

        $article->approve();
        $article->save();

        $this->redirect('http://'.$this->domainName.Helper\Url::build($article->publishedAt, $article->slug));
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

} 
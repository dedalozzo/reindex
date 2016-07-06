<?php

/**
 * @file ArticleController.php
 * @brief This file contains the ArticleController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Controller;


use ReIndex\Validation;
use ReIndex\Helper;
use ReIndex\Exception\InvalidFieldException;
use ReIndex\Doc\Article;

use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Mvc\View;


/**
 * @brief Controller of Article actions.
 * @nosubgrouping
 */
final class ArticleController extends IndexController {


  /**
   * @copydoc IndexController::getLabel()
   */
  protected function getLabel() {
    return 'articles';
  }


  /**
   * @copydoc IndexController::popular()
   */
  protected function popular($filter, $unversionTagId = NULL) {
    $this->periods = Helper\ArrayHelper::slice($this->periods, 5);
    parent::popular($filter, $unversionTagId);
  }


  /**
   * @brief Creates a new article.
   */
  public function newAction() {
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

        $validation->add("tags", new PresenceOf(["message" => "You must add at least one tag."]));

        $group = $validation->validate($_POST);
        if (count($group) > 0) {
          throw new InvalidFieldException("Fields are incomplete or the entered values are invalid. The errors are reported in red under the respective entry fields.");
        }

        $article = Article::create();
        $article->title = $this->request->getPost('title');
        $article->body = $this->request->getPost('body');
        $article->creatorId = $this->user->id;
        $article->tags->addMultipleAtOnce($this->request->getPost('tags'));

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
    $this->view->setVar('title', 'New article');

    $this->view->disableLevel(View::LEVEL_LAYOUT);

    // Adds Selectize Plugin files.
    $this->assets->addJs($this->dist."/js/selectize.min.js", FALSE);
    $this->addCodeMirror();

    $this->view->pick('views/post/new');
  }

} 
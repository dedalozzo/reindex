<?php

//! @file InitCommand.php
//! @brief This file contains the InitCommand class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use ElephantOnCouch\Couch;
use ElephantOnCouch\Doc\DesignDoc;
use ElephantOnCouch\Handler\ViewHandler;


//! @brief Initializes the PitPress database, adding the required design documents.
//! @nosubgrouping
class InitCommand extends AbstractCommand {

  protected $mysql;
  protected $couch;


  //! @brief Insert all design documents.
  private function initAll() {
    $this->initIndex();
    $this->initBlog();
    $this->initForum();
    $this->initLinks();
    $this->initTags();
    $this->initBadges();
    $this->initUsers();
    $this->initHits();
    $this->initVotes();
    $this->initFavourites();
    $this->initComments();
    $this->initAnswers();
  }


  private function initIndex() {
    $doc = DesignDoc::create('index');

    // Shows the most popular updates: posts (articles, books, tutorials), questions, links.
    function recent() {
      $map = "function(\$doc) use (\$emit) {
                \$types = [
                  'article' => NULL,
                  'tutorial' => NULL,
                  'book' => NULL,
                  'question' => NULL,
                  'link' => NULL
                ];

                if (array_key_exists(\$doc->type, \$types)
                  \$emit(\$doc->_id);
              };";

      $handler = new ViewHandler("all");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the updates.

      return $handler;
    }

    // Shows the most recent updates: posts (articles, books, tutorials), questions, links.
    function recent() {
      $map = "function(\$doc) use (\$emit) {
                \$types = [
                  'article' => NULL,
                  'tutorial' => NULL,
                  'book' => NULL,
                  'question' => NULL,
                  'link' => NULL
                ];

                if (array_key_exists(\$doc->type, \$types)
                  \$emit(\$doc->_id, \$doc->_id);
              };";

      $handler = new ViewHandler("all");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the updates.

      return $handler;
    }


    $doc->addHandler(popular());
    $doc->addHandler(recent());
    $doc->addHandler(basedOnMyTags());
    $doc->addHandler(mostVoted());
    $doc->addHandler(mostDiscussesed());
    $doc->addHandler(rss());
  }


  private function initBlog() {

  }


  private function initForum() {

  }


  private function initLinks() {

  }


  private function initTags() {
    //Then, we must also create two different views (associated_tags, related_items), the first
//! one will emit as key the item's ID and the classification document as value, the second one, instead, will emit the
//! tag's ID as key and always the classification's document as value. It's important emit always the entire classification
//! because it's easy to query items by tag and query tags by item
  }

  private function initBadges() {

  }


  private function initUsers() {

  }


  private function initHits() {
    $doc = DesignDoc::create('hits');

    // Counts the hits for the given id.
    function all() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'hit')
                  \$emit(\$doc->docId, NULL);
              };";

      $handler = new ViewHandler("all");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(all());

    $this->couch->saveDoc($doc);
  }


  private function initVotes() {

  }


  private function initFavourites() {
    $doc = DesignDoc::create('favourites');

    // @name items_starred_by_user
    // @brief Returns the items that have been starred by the user.
    $map = "function(\$doc) use (\$emit) {
              if (\$doc->type == 'favourite')
                \$emit(\$doc->userId, \$doc->itemId);
            };";

    $handler = new ViewHandler("items_starred_by_user");
    $handler->mapFn = $map;

    $doc->addHandler($handler);


    // @name items_starred_by_user
    // @brief Given a key, composed by the itemId plus the userId, returns the which items included in the list are
    // starred by the user.
    // @key
    $map = "function(\$doc) use (\$emit) {
              if (\$doc->type == 'favourite')
                \$emit(\$doc->itemId + \$doc->userId, NULL);
            };";

    $handler = new ViewHandler("starred_items");
    $handler->mapFn = $map;

    $doc->addHandler($handler);


    // @name users_count_by_item
    // @brief
    // @key userId
    $map = "function(\$doc) use (\$emit) {
              if (\$doc->type == 'favourite')
                \$emit(\$doc->itemId, \$doc->userId);
            };";

    $handler = new ViewHandler("user_favourites");
    $handler->mapFn = $map;
    $handler->useBuiltInReduceFnCount();

    $doc->addHandler($handler);


    $this->couch->saveDoc($doc);
  }


  private function initComments() {
    $doc = DesignDoc::create('comments');

    function recent() {
      $map = "function(\$doc) use (\$emit) {
              if (\$doc->type == 'hit')
                \$emit(\$doc->_id, NULL);
            };";

      $handler = new ViewHandler("all");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }


    $doc->addHandler(popular());
    $doc->addHandler(recent());
    $doc->addHandler(basedOnMyTags());
    $doc->addHandler(mostVoted());
    $doc->addHandler(mostDiscussesed());
    $doc->addHandler(rss());

    if (doc.type == "post") {
      map([doc._id, 0], doc);
    } else if (doc.type == "comment") {
      map([doc.post, 1], doc);
    }

  }


  private function initAnswers() {

  }


  //! @brief Configures the command.
  protected function configure() {
    $this->setName("init");
    $this->setDescription("Initializes the PitPress database, adding the required design documents.");
    $this->addArgument("documents",
      InputArgument::IS_ARRAY | InputArgument::REQUIRED,
      "The documents containing the views you want create. Use 'all' if you want insert all the documents, 'users' if
      you want just init the users or separate multiple documents with a space. The available documents are: users,
      articles, books, tags");
  }


  //! @brief Executes the command.
  protected function execute(InputInterface $input, OutputInterface $output) {

    $this->mysql = $this->_di['mysql'];
    $this->couch = $this->_di['couchdb'];

    $entities = $input->getArgument('documents');

    // Checks if the argument 'all' is provided.
    $index = array_search("all", $documents);

    if ($index === FALSE) {

      foreach ($documents as $name)
        switch ($name) {
          case 'users':
            $this->importUsers();
            break;

          case 'articles':
            $this->importArticles();
            break;

          case 'books':
            $this->importBooks();
            break;

          case 'tags':
            $this->importTags();
            break;

          case 'classifications':
            $this->importClassifications();
            break;
        }

    }
    else
      $this->initAll();
  }

}
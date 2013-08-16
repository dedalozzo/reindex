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

use ElephantOnCouch\Doc\DesignDoc;
use ElephantOnCouch\Handler\ViewHandler;


//! @brief Initializes the PitPress database, adding the required design documents.
//! @nosubgrouping
class InitCommand extends AbstractCommand {

  protected $mysql;
  protected $couch;


  //! @brief Insert all design documents.
  private function initAll() {
    $this->initPosts();
    $this->initTags();
    $this->initVotes();
    $this->initStars();
    $this->initSubscriptions();
    $this->initClassifications();
    $this->initBadges();
    $this->initFavourites();
  }


  private function initPosts() {
    $doc = DesignDoc::create('posts');


    // @params: NONE
    function allLatest() {
      $map = "function(\$doc) use (\$emit) {
                if (isset(\$doc->supertype) and \$doc->supertype == 'post')
                  \$emit(\$doc->publishingDate);
              };";

      $handler = new ViewHandler("allLatest");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(allLatest());


    // @params: type
    function typeLatest() {
      $map = "function(\$doc) use (\$emit) {
                if (isset(\$doc->supertype) and \$doc->supertype == 'post')
                  \$emit([\$doc->type, \$doc->publishingDate]);
              };";

      $handler = new ViewHandler("typeLatest");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(typeLatest());


    // @params: section
    function sectionLatest() {
      $map = "function(\$doc) use (\$emit) {
                if (isset(\$doc->section))
                  \$emit([\$doc->section, \$doc->publishingDate]);
              };";

      $handler = new ViewHandler("sectionLatest");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(sectionLatest());


    // @params: NONE
    function allMostVoted() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'vote') {
                  if (\$doc->choice == '+')
                    \$emit(\$doc->postId, 1);
                  elseif (\$doc->choice == '-')
                    \$emit(\$doc->postId, -1);
                }
              };";

      $handler = new ViewHandler("allMostVoted");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnSum(); // Used to count the votes.

      return $handler;
    }

    $doc->addHandler(allMostVoted());


    // @params: type, [postId]
    function typeMostVoted() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'vote') {
                  if (\$doc->choice == '+')
                    \$emit([\$doc->postType, \$doc->postId], 1);
                  elseif (\$doc->choice == '-')
                    \$emit([\$doc->postType, \$doc->postId], -1);
                }
              };";

      $handler = new ViewHandler("typeMostVoted");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnSum(); // Used to count the votes.

      return $handler;
    }

    $doc->addHandler(typeMostVoted());


    // @params: section, [postId]
    function sectionMostVoted() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'vote') {
                  if (\$doc->choice == '+')
                    \$emit([\$doc->section, \$doc->postId], 1);
                  elseif (\$doc->choice == '-')
                    \$emit([\$doc->section, \$doc->postId], -1);
                }
              };";

      $handler = new ViewHandler("sectionMostVoted");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnSum(); // Used to count the votes.

      return $handler;
    }

    $doc->addHandler(sectionMostVoted());


    $this->couch->saveDoc($doc);
  }


  private function initTags() {
    $doc = DesignDoc::create('tags');


    // @params NONE
    function all() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'tag')
                  \$emit(\$doc->_id, \$doc->name);
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
    $doc = DesignDoc::create('votes');


    // @params: postId, [userId]
    // @methods: Post.isVoted(), Post.getVotesCount()
    function votesPerPost() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'vote') {
                  if (\$doc->choice == '+')
                    \$emit([\$doc->postId, \$doc->userId], 1);
                  elseif (\$doc->choice == '-')
                    \$emit([\$doc->postId, \$doc->userId], -1);
                }
              };";

      $handler = new ViewHandler("perPost");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnSum(); // Used to count the votes.

      return $handler;
    }

    $doc->addHandler(votesPerPost());


    $this->couch->saveDoc($doc);
  }


  private function initStars() {
    $doc = DesignDoc::create('stars');


    // @params postId, [userId]
    // @methods: VersionedItem.isStarred(), VersionedItem.getStarsCount()
    function starsPerItem() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'star')
                  \$emit([\$doc->itemId, \$doc->userId]);
              };";

      $handler = new ViewHandler("perItem");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(starsPerItem());


    $this->couch->saveDoc($doc);
  }


  private function initSubscriptions() {
    $doc = DesignDoc::create('subscriptions');


    // @params itemId, [userId]
    // @methods: VersionedItem.isStarred(), VersionedItem.getSubscribersCount()
    function subscriptionsPerItem() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'subscription')
                  \$emit([\$doc->itemId, \$doc->userId]);
              };";

      $handler = new ViewHandler("perItem");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(subscriptionsPerItem());


    $this->couch->saveDoc($doc);
  }


  private function initClassifications() {
    $doc = DesignDoc::create('classifications');


    // @params postId
    // @methods: Post.getTags()
    function classificationsPerPost() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'classification')
                  \$emit(\$doc->postId, \$doc->tagId);
              };";

      $handler = new ViewHandler("perPost");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(classificationsPerPost());


    // @params NONE
    function classificationsAllLatest() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'classification')
                  \$emit(\$doc->timestamp, \$doc->tagId);
              };";

      $handler = new ViewHandler("allLatest");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(classificationsAllLatest());


    // @params tagId
    function perTag() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'classification')
                  \$emit(\$doc->tagId);
              };";

      $handler = new ViewHandler("perTag");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(perTag());


    $this->couch->saveDoc($doc);
  }


  private function initBadges() {
  }


  private function initReputation() {
    $doc = DesignDoc::create('stars');


    // @params userId, [timestamp]
    // @methods: User.getReputation()
    function reputationPerUser() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'reputation')
                  \$emit([\$doc->userId, \$doc->timestamp], \$doc->points);
              };";

      $handler = new ViewHandler("perUser");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnSum();

      return $handler;
    }

    $doc->addHandler(reputationPerUser());


    $this->couch->saveDoc($doc);
  }


  private function initFavourites() {
    $doc = DesignDoc::create('favourites');


    // @params userId
    // @methods: todo
    function allLastAdded() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'star')
                  \$emit([\$doc->userId, \$doc->timestamp], \$doc->postId);
              };";

      $handler = new ViewHandler("allLastAdded");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(allLastAdded());


    // @params userId, type
    // @methods: todo
    function typeLastAdded() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'star')
                  \$emit([\$doc->userId, \$doc->postType, \$doc->timestamp], \$doc->postId);
              };";

      $handler = new ViewHandler("typeLastAdded");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(typeLastAdded());


    $this->couch->saveDoc($doc);
  }


  private function initUsers() {
    $doc = DesignDoc::create('users');


    // @params NONE
    function allNames() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'user')
                  \$emit(\$doc->_id, \$doc->displayName);
              };";

      $handler = new ViewHandler("all");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(allNames());


    $this->couch->saveDoc($doc);
  }


  //! @brief Configures the command.
  protected function configure() {
    $this->setName("init");
    $this->setDescription("Initializes the PitPress database, adding the required design documents.");
    $this->addArgument("documents",
      InputArgument::IS_ARRAY | InputArgument::REQUIRED,
      "The documents containing the views you want create. Use 'all' if you want insert all the documents, 'users' if
      you want just init the users or separate multiple documents with a space. The available documents are: users,
      articles, books, tags, reputation.");
  }


  //! @brief Executes the command.
  protected function execute(InputInterface $input, OutputInterface $output) {

    $this->mysql = $this->_di['mysql'];
    $this->couch = $this->_di['couchdb'];

    $documents = $input->getArgument('documents');

    // Checks if the argument 'all' is provided.
    $index = array_search("all", $documents);

    if ($index === FALSE) {

      foreach ($documents as $name)
        switch ($name) {
          case 'posts':
            $this->initPosts();
            break;

          case 'tags':
            $this->initTags();
            break;

          case 'votes':
            $this->initVotes();
            break;

          case 'stars':
            $this->initStars();
            break;

          case 'subscriptions':
            $this->initSubscriptions();
            break;

          case 'classifications':
            $this->initClassifications();
            break;

          case 'badges':
            $this->initBadges();
            break;

          case 'favourites':
            $this->initFavourites();
            break;

          case 'users':
            $this->initUsers();
            break;

          case 'reputation':
            $this->initReputation();
            break;
        }

    }
    else
      $this->initAll();
  }

}
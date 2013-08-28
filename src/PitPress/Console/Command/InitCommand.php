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
    $this->initReputation();
    $this->initBadges();
    $this->initFavorites();
    $this->initUsers();
  }


  private function initPosts() {
    $doc = DesignDoc::create('posts');


    // @params: NONE
    function allPosts() {
      $map = "function(\$doc) use (\$emit) {
                if (isset(\$doc->supertype) and \$doc->supertype == 'post')
                  \$emit(\$doc->_id, [
                     'title' => \$doc->title,
                     'excerpt' => \$doc->excerpt,
                     'url' => \$doc->url,
                     'publishingType' => \$doc->publishingType,
                     'publishingDate' => \$doc->publishingDate,
                     'userId' => \$doc->userId,
                     'username' => \$doc->username
                   ]);
              };";

      $handler = new ViewHandler("all");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(allPosts());


    // @params: NONE
    function latestPosts() {
      $map = "function(\$doc) use (\$emit) {
                if (isset(\$doc->supertype) and \$doc->supertype == 'post')
                  \$emit(\$doc->publishingDate);
              };";

      $handler = new ViewHandler("latest");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(latestPosts());


    // @params: NONE
    function postsPerDate() {
      $map = "function(\$doc) use (\$emit) {
                if (isset(\$doc->supertype) and \$doc->supertype == 'post')
                  \$emit([\$doc->year, \$doc->month, \$doc->day]);
              };";

      $handler = new ViewHandler("perDate");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(postsPerDate());


    // @params: type
    function latestPostsPerType() {
      $map = "function(\$doc) use (\$emit) {
                if (isset(\$doc->supertype) and \$doc->supertype == 'post')
                  \$emit([\$doc->type, \$doc->publishingDate]);
              };";

      $handler = new ViewHandler("latestPerType");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(latestPostsPerType());


    // @params: section
    function latestPostsPerSection() {
      $map = "function(\$doc) use (\$emit) {
                if (isset(\$doc->section))
                  \$emit([\$doc->section, \$doc->publishingDate]);
              };";

      $handler = new ViewHandler("latestPerSection");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(latestPostsPerSection());


    // @params: NONE
    function mostVotedPosts() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'vote') {
                  if (\$doc->choice == '+')
                    \$emit(\$doc->postId, 1);
                  elseif (\$doc->choice == '-')
                    \$emit(\$doc->postId, -1);
                }
              };";

      $handler = new ViewHandler("mostVoted");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnSum(); // Used to count the votes.

      return $handler;
    }

    $doc->addHandler(mostVotedPosts());


    // @params: type, [postId]
    function mostVotedPostsPerType() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'vote') {
                  if (\$doc->choice == '+')
                    \$emit([\$doc->postType, \$doc->postId], 1);
                  elseif (\$doc->choice == '-')
                    \$emit([\$doc->postType, \$doc->postId], -1);
                }
              };";

      $handler = new ViewHandler("mostVotedPerType");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnSum(); // Used to count the votes.

      return $handler;
    }

    $doc->addHandler(mostVotedPostsPerType());


    // @params: section, [postId]
    function mostVotedPostsPerSection() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'vote') {
                  if (\$doc->choice == '+')
                    \$emit([\$doc->section, \$doc->postId], 1);
                  elseif (\$doc->choice == '-')
                    \$emit([\$doc->section, \$doc->postId], -1);
                }
              };";

      $handler = new ViewHandler("mostVotedPerSection");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnSum(); // Used to count the votes.

      return $handler;
    }

    $doc->addHandler(mostVotedPostsPerSection());


    $this->couch->saveDoc($doc);
  }


  private function initTags() {
    $doc = DesignDoc::create('tags');


    // @params NONE
    function allTags() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'tag')
                  \$emit(\$doc->_id, \$doc->name);
              };";

      $handler = new ViewHandler("all");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(allTags());


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
    function latestClassifications() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'classification')
                  \$emit(\$doc->timestamp, \$doc->tagId);
              };";

      $handler = new ViewHandler("latest");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(latestClassifications());


    // @params tagId
    function classificationsPerTag() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'classification')
                  \$emit(\$doc->tagId);
              };";

      $handler = new ViewHandler("perTag");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(classificationsPerTag());


    $this->couch->saveDoc($doc);
  }


  private function initBadges() {
  }


  private function initReputation() {
    $doc = DesignDoc::create('reputation');


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


  private function initFavorites() {
    $doc = DesignDoc::create('favorites');


    // @params userId
    // @methods: todo
    function lastAddedFavorites() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'star')
                  \$emit([\$doc->userId, \$doc->timestamp], \$doc->postId);
              };";

      $handler = new ViewHandler("lastAdded");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(lastAddedFavorites());


    // @params userId, type
    // @methods: todo
    function lastAddedFavoritesPerType() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'star')
                  \$emit([\$doc->userId, \$doc->postType, \$doc->timestamp], \$doc->postId);
              };";

      $handler = new ViewHandler("lastAddedPerType");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(lastAddedFavoritesPerType());


    $this->couch->saveDoc($doc);
  }


  private function initUsers() {
    $doc = DesignDoc::create('users');


    // @params: [userId]
    function allUserNames() {
      $map = "function(\$doc) use (\$emit) {
                if (\$doc->type == 'user')
                  \$emit(\$doc->_id, \$doc->displayName);
              };";

      $handler = new ViewHandler("allNames");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(allUserNames());


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
            $this->initFavorites();
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
<?php

/*
 * @file InitCommand.php
 * @brief This file contains the InitCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use ElephantOnCouch\Doc\DesignDoc;
use ElephantOnCouch\Handler\ViewHandler;


/**
 * @brief Initializes the PitPress database, adding the required design documents.
 * @nosubgrouping
 */
class InitCommand extends AbstractCommand {

  protected $mysql;
  protected $couch;


  /**
   * @brief Insert all design documents.
   */
  protected function initAll() {
    $this->initDocs();
    $this->initPosts();
    $this->initTags();
    $this->initVotes();
    $this->initScores();
    $this->initStars();
    $this->initSubscriptions();
    $this->initClassifications();
    $this->initReputation();
    $this->initBadges();
    $this->initFavorites();
    $this->initUsers();
    $this->initReplies();
  }


  protected function initDocs() {
    $doc = DesignDoc::create('docs');

    function docsByType() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  $emit($doc->type);
};
MAP;

      $handler = new ViewHandler("byType");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(docsByType());


    $this->couch->saveDoc($doc);
  }


  protected function initPosts() {
    $doc = DesignDoc::create('posts');


    // @params: NONE
    function allPosts() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) and $doc->supertype == 'post')
    $emit(strtok($doc->_id, '::'), [
       'type' => $doc->type,
       'title' => $doc->title,
       'excerpt' => $doc->excerpt,
       'slug' => $doc->slug,
       'publishingDate' => $doc->publishingDate,
       'userId' => $doc->userId
     ]);
};
MAP;

      $handler = new ViewHandler("all");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(allPosts());


    // @params: year, month, day, slug
    function postsByUrl() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) and $doc->supertype == 'post')
    $emit([$doc->year, $doc->month, $doc->day, $doc->slug]);
};
MAP;

      $handler = new ViewHandler("byUrl");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(postsByUrl());


    // @params: NONE
    function postsPerDate() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) and $doc->supertype == 'post')
    $emit($doc->publishingDate);
};
MAP;

      $handler = new ViewHandler("perDate");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(postsPerDate());


    // @params: type
    function postsPerDateByType() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) and $doc->supertype == 'post')
    $emit([$doc->type, $doc->publishingDate]);
};
MAP;

      $handler = new ViewHandler("perDateByType");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(postsPerDateByType());


    // @params: userId
    function postsPerDateByUser() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) and $doc->supertype == 'post')
    $emit([$doc->userId, $doc->publishingDate]);
};
MAP;

      $handler = new ViewHandler("perDateByUser");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(postsPerDateByUser());


    // @params: userId, type
    function postsPerDateByUserAndType() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) and $doc->supertype == 'post')
    $emit([$doc->userId, $doc->type, $doc->publishingDate]);
};
MAP;

      $handler = new ViewHandler("perDateByUserAndType");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(postsPerDateByUserAndType());


    $this->couch->saveDoc($doc);
  }


  protected function initTags() {
    $doc = DesignDoc::create('tags');


    function allTags() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'tag')
    $emit(strtok($doc->_id, '::'), [$doc->name, $doc->excerpt, $doc->publishingDate]);
};
MAP;

      $handler = new ViewHandler("all");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(allTags());


    function allNames() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'tag')
    $emit(strtok($doc->_id, '::'), $doc->name);
};
MAP;

      $handler = new ViewHandler("allNames");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(allNames());


    function newestTags() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'tag')
    $emit($doc->publishingDate);
};
MAP;

      $handler = new ViewHandler("newest");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(newestTags());


    function tagsByName() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'tag')
    $emit($doc->name);
};
MAP;

      $handler = new ViewHandler("byName");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(tagsByName());


    $this->couch->saveDoc($doc);
  }


  protected function initVotes() {
    $doc = DesignDoc::create('votes');


    // @params: [postId]
    function votesPerPost() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'vote')
    $emit($doc->postId, $doc->value);
};
MAP;

      $handler = new ViewHandler("perPost");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnSum(); // Used to count the votes.

      return $handler;
    }

    $doc->addHandler(votesPerPost());


    // @params: postId, userId
    function votesPerPostAndUser() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'vote')
    $emit([$doc->postId, $doc->userId], $doc->value);
};
MAP;

      $handler = new ViewHandler("perPostAndUser");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnSum(); // Used to count the votes.

      return $handler;
    }

    $doc->addHandler(votesPerPostAndUser());


    // @params: [userId]
    function votesPerUser() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'vote')
    $emit($doc->userId);
};
MAP;

      $handler = new ViewHandler("perUser");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(votesPerUser());


    // @params: type, postId
    function votesPerType() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'vote')
    $emit([$doc->postType, $doc->postId], $doc->value);
};
MAP;

      $handler = new ViewHandler("perType");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnSum(); // Used to count the votes.

      return $handler;
    }

    $doc->addHandler(votesPerType());


    // @params: timestamp
    function votesNotRecorded() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (($doc->type == 'vote') && (!$doc->recorded))
    $emit($doc->timestamp, $doc);
};
MAP;

      $handler = new ViewHandler("notRecorded");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(votesNotRecorded());


    $this->couch->saveDoc($doc);
  }


  protected function initScores() {
    $doc = DesignDoc::create('scores');


    // @params postId
    function scoresPerPost() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'score')
    $emit($doc->postId, $doc);
};
MAP;

      $handler = new ViewHandler("perPost");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(scoresPerPost());


    // @params: type
    function scoresPerType() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'score')
    $emit([$doc->postType, $doc->points], $doc->postId);
};
MAP;

      $handler = new ViewHandler("perType");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(scoresPerType());


    $this->couch->saveDoc($doc);
  }


  protected function initStars() {
    $doc = DesignDoc::create('stars');


    // @params postId, [userId]
    // @methods: VersionedItem.isStarred(), VersionedItem.getStarsCount()
    function starsPerItem() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'star')
    $emit([$doc->itemId, $doc->userId]);
};
MAP;

      $handler = new ViewHandler("perItem");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(starsPerItem());


    $this->couch->saveDoc($doc);
  }


  protected function initSubscriptions() {
    $doc = DesignDoc::create('subscriptions');


    // @params itemId, [userId]
    // @methods: VersionedItem.isStarred(), VersionedItem.getSubscribersCount()
    function subscriptionsPerItem() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'subscription')
    $emit([$doc->itemId, $doc->userId]);
};
MAP;

      $handler = new ViewHandler("perItem");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(subscriptionsPerItem());


    $this->couch->saveDoc($doc);
  }


  protected function initClassifications() {
    $doc = DesignDoc::create('classifications');


    // @params postId
    // @methods: Post.getTags()
    function classificationsPerPost() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'classification')
    $emit($doc->postId, $doc->tagId);
};
MAP;

      $handler = new ViewHandler("perPost");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(classificationsPerPost());


    // @params NONE
    function newestClassifications() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'classification')
    $emit($doc->timestamp, $doc->tagId);
};
MAP;

      $handler = new ViewHandler("newest");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(newestClassifications());


    // @params tagId
    function classificationsPerTag() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'classification')
    $emit($doc->tagId);
};
MAP;

      $handler = new ViewHandler("perTag");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(classificationsPerTag());


    $this->couch->saveDoc($doc);
  }


  protected function initBadges() {
    $doc = DesignDoc::create('badges');


    // @params class
    function badgesPerClass() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'badge')
    $emit($doc->class);
};
MAP;

      $handler = new ViewHandler("perClass");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(badgesPerClass());


    // @params class, userId
    function badgesPerClassAndUser() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'badge')
    $emit([$doc->class, $doc->userId]);
};
MAP;

      $handler = new ViewHandler("perClassAndUser");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(badgesPerClassAndUser());


    $this->couch->saveDoc($doc);
  }


  protected function initReputation() {
    $doc = DesignDoc::create('reputation');


    // @params userId, [timestamp]
    // @methods: User.getReputation()
    function reputationPerUser() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'reputation')
    $emit([$doc->userId, $doc->timestamp], $doc->points);
};
MAP;

      $handler = new ViewHandler("perUser");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnSum();

      return $handler;
    }

    $doc->addHandler(reputationPerUser());


    $this->couch->saveDoc($doc);
  }


  protected function initFavorites() {
    $doc = DesignDoc::create('favorites');


    // @params: userId
    function favoritesPerDateAdded() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'star')
    $emit([$doc->userId, $doc->dateAdded], $doc->itemId);
};
MAP;

      $handler = new ViewHandler("perDateAdded");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(favoritesPerDateAdded());


    // @params: userid, type
    function favoritesPerDateAddedByType() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'star')
    $emit([$doc->userId, $doc->itemType, $doc->dateAdded], $doc->itemId);
};
MAP;

      $handler = new ViewHandler("perDateAddedByType");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(favoritesPerDateAddedByType());


    // @params: userId
    function favoritesPerPublishingDate() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'star')
    $emit([$doc->userId, $doc->itemPublishingDate], $doc->itemId);
};
MAP;

      $handler = new ViewHandler("perPublishingDate");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(favoritesPerPublishingDate());


    // @params: userid, type
    function favoritesPerPublishingDateByType() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'star')
    $emit([$doc->userId, $doc->itemType, $doc->itemPublishingDate], $doc->itemId);
};
MAP;

      $handler = new ViewHandler("perPublishingDateByType");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(favoritesPerPublishingDateByType());


    $this->couch->saveDoc($doc);
  }


  protected function initUsers() {
    $doc = DesignDoc::create('users');


    // @params: [userId]
    function allUsers() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'user')
    $emit($doc->_id, [$doc->username, $doc->email, $doc->creationDate]);
};
MAP;

      $handler = new ViewHandler("all");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(allUsers());


    // @params: [userId]
    function allUserNames() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'user')
    $emit($doc->_id, [$doc->username, $doc->email]);
};
MAP;

      $handler = new ViewHandler("allNames");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(allUserNames());


    function newestUsers() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'user')
    $emit($doc->creationDate);
};
MAP;

      $handler = new ViewHandler("newest");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(newestUsers());


    function usersByUsername() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'user')
    $emit($doc->username, $doc->_id);
};
MAP;

      $handler = new ViewHandler("byUsername");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(usersByUsername());


    function usersByEmail() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'user')
    $emit($doc->email);
};
MAP;

      $handler = new ViewHandler("byEmail");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(usersByEmail());


    // @params: [postId]
    function usersHaveVoted() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'vote')
    $emit($doc->postId, $doc->userId);
};
MAP;

      $handler = new ViewHandler("haveVoted");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(usersHaveVoted());


    $this->couch->saveDoc($doc);
  }


  protected function initReplies() {
    $doc = DesignDoc::create('replies');


    // @params postId
    function repliesPerPost() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) and $doc->supertype == 'reply')
    $emit($doc->postId);
};
MAP;

      $handler = new ViewHandler("perPost");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(repliesPerPost());


    // @params: postId
    function newestRepliesPerPost() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) and $doc->supertype == 'reply')
    $emit([$doc->postId, $doc->publishingDate]);
};
MAP;

      $handler = new ViewHandler("newestPerPost");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(newestRepliesPerPost());


    // @params: postId
    function lastUpdatedRepliesPerPost() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) and $doc->supertype == 'reply')
    $emit([$doc->postId, $doc->lastUpdate]);
};
MAP;

      $handler = new ViewHandler("lastUpdatedPerPost");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(lastUpdatedRepliesPerPost());


    $this->couch->saveDoc($doc);
  }


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("init");
    $this->setDescription("Initializes the PitPress database, adding the required design documents.");
    $this->addArgument("documents",
      InputArgument::IS_ARRAY | InputArgument::REQUIRED,
      "The documents containing the views you want create. Use 'all' if you want insert all the documents, 'users' if
      you want just init the users or separate multiple documents with a space. The available documents are: docs, posts,
      tags, votes, scores, stars, subscriptions, classifications, badges, favorites, users, reputation, replies.");
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {

    $this->mysql = $this->di['mysql'];
    $this->couch = $this->di['couchdb'];

    $documents = $input->getArgument('documents');

    // Checks if the argument 'all' is provided.
    $index = array_search("all", $documents);

    if ($index === FALSE) {

      foreach ($documents as $name)
        switch ($name) {
          case 'docs':
            $this->initDocs();
            break;

          case 'posts':
            $this->initPosts();
            break;

          case 'tags':
            $this->initTags();
            break;

          case 'votes':
            $this->initVotes();
            break;

          case 'scores':
            $this->initScores();
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

          case 'favorites':
            $this->initFavorites();
            break;

          case 'users':
            $this->initUsers();
            break;

          case 'reputation':
            $this->initReputation();
            break;

          case 'replies':
            $this->initReplies();
            break;
        }

    }
    else
      $this->initAll();

    parent::execute($input, $output);
  }

}
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
    $this->initRevisions();
    $this->initVotes();
    $this->initScores();
    $this->initStars();
    $this->initSubscriptions();
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
  if (isset($doc->supertype) && $doc->supertype == 'post')
    $emit($doc->_id, [
        'type' => $doc->type,
        'status' => $doc->status,
        'title' => $doc->title,
        'excerpt' => $doc->excerpt,
        'slug' => $doc->slug,
        'createdAt' => $doc->createdAt,
        'modifiedAt' => $doc->modifiedAt,
        'publishedAt' => $doc->publishedAt,
        'creatorId' => $doc->creatorId,
        'tags' => $doc->tags
      ]);
};
MAP;

      $handler = new ViewHandler("all");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(allPosts());


    // @params: NONE
    function unversionPosts() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->status == 'current')
    $emit($doc->unversionId);
};
MAP;

      $handler = new ViewHandler("unversion");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(unversionPosts());


    // @params: year, month, day, slug
    function postsByUrl() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->status == 'current')
    $emit([$doc->year, $doc->month, $doc->day, $doc->slug]);
};
MAP;

      $handler = new ViewHandler("byUrl");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(postsByUrl());


    // @params: year, month, day, slug
    function approvedRevisionsByUrl() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->status == 'approved')
    $emit([$doc->year, $doc->month, $doc->day, $doc->slug]);
};
MAP;

      $handler = new ViewHandler("approvedRevisionsByUrl");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(approvedRevisionsByUrl());


    // @params: NONE
    function postsPerDate() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->index && $doc->status == 'current')
    $emit($doc->publishedAt);
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
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->status == 'current')
    $emit([$doc->type, $doc->publishedAt]);
};
MAP;

      $handler = new ViewHandler("perDateByType");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(postsPerDateByType());


    // @params: NONE
    function postsPerDateByTag() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->index && $doc->status == 'current' && isset($doc->tags))
    foreach ($doc->tags as $tagId)
      $emit([$tagId, $doc->publishedAt]);
};
MAP;

      $handler = new ViewHandler("perDateByTag");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(postsPerDateByTag());


    // @params: type
    function postsPerDateByTagAndType() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->status == 'current' && isset($doc->tags))
    foreach ($doc->tags as $tagId)
      $emit([$tagId, $doc->type, $doc->publishedAt]);
};
MAP;

      $handler = new ViewHandler("perDateByTagAndType");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(postsPerDateByTagAndType());


    // @params: creatorId
    function postsPerDateByUser() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->index && $doc->status == 'current')
    $emit([$doc->creatorId, $doc->publishedAt]);
};
MAP;

      $handler = new ViewHandler("perDateByUser");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(postsPerDateByUser());


    // @params: creatorId, type
    function postsPerDateByUserAndType() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->status == 'current')
    $emit([$doc->creatorId, $doc->type, $doc->publishedAt]);
};
MAP;

      $handler = new ViewHandler("perDateByUserAndType");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(postsPerDateByUserAndType());


    function postsPerTag() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->status == 'current' && isset($doc->tags))
    foreach ($doc->tags as $tagId)
      $emit($tagId);
};
MAP;

      $handler = new ViewHandler("perTag");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(postsPerTag());


    $this->couch->saveDoc($doc);
  }


  protected function initTags() {
    $doc = DesignDoc::create('tags');


    function allTags() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'tag')
    $emit($doc->_id, [$doc->name, $doc->excerpt, $doc->createdAt]);
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
  if ($doc->type == 'tag' && $doc->status == 'current')
    $emit($doc->unversionId, $doc->name);
};
MAP;

      $handler = new ViewHandler("allNames");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(allNames());


    function substrings() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'tag' && $doc->status == 'current') {
    $str = preg_replace('/-/su', '', $doc->name);
    $length = mb_strlen($str, 'UTF-8');

    $subs = [];
    for ($i = 0; $i < $length; $i++)
      for ($j = 1; $j <= $length; $j++)
        $subs[] = mb_substr($str, $i, $j, 'UTF-8');

    $subs = array_unique($subs);

    foreach ($subs as $substring)
      $emit($substring);
  }
};
MAP;

      $handler = new ViewHandler("substrings");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(substrings());


    function newestTags() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'tag' && $doc->status == 'current')
    $emit($doc->createdAt);
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
  if ($doc->type == 'tag' && $doc->status == 'current')
    $emit($doc->name);
};
MAP;

      $handler = new ViewHandler("byName");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(tagsByName());


    function tagsPerPost() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->status == 'current' && isset($doc->tags))
    foreach ($doc->tags as $tagId)
      $emit($doc->unversionId, $tagId);
};
MAP;

      $handler = new ViewHandler("perPost");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(tagsPerPost());


    $this->couch->saveDoc($doc);
  }


  protected function initRevisions() {
    $doc = DesignDoc::create('revisions');


    // @params: itemId
    function revisionsPerItem() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->versionable)) {

    $editorId = isset($doc->editorId) ? $doc->editorId : $doc->creatorId;
    $editSummary = isset($doc->editSummary) ? $doc->editSummary : '';

    $emit($doc->unversionId, [
        'modifiedAt' => $doc->modifiedAt,
        'editorId' => $editorId,
        'editSummary' => $editSummary
      ]);
  }
};
MAP;

      $handler = new ViewHandler("perItem");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(revisionsPerItem());


    $this->couch->saveDoc($doc);
  }


  protected function initVotes() {
    $doc = DesignDoc::create('votes');


    // @params: [itemId]
    function votesPerItem() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'vote')
    $emit($doc->itemId, $doc->value);
};
MAP;

      $handler = new ViewHandler("perItem");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnSum(); // Used to count the votes.

      return $handler;
    }

    $doc->addHandler(votesPerItem());


    // @params: itemId, userId
    function votesPerItemAndUser() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'vote')
    $emit([$doc->itemId, $doc->userId], $doc->value);
};
MAP;

      $handler = new ViewHandler("perItemAndUser");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnSum(); // Used to count the votes.

      return $handler;
    }

    $doc->addHandler(votesPerItemAndUser());


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
    function favoritesByUserTags() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'star' && $doc->itemType == 'tag')
    $emit($doc->userId, $doc->itemId);
};
MAP;

      $handler = new ViewHandler("byUserTags");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(favoritesByUserTags());


    // @params: userId
    function favoritesPerAddedAt() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'star' && isset($doc->itemSupertype) && $doc->itemSupertype == 'post' && $doc->index)
    $emit([$doc->userId, $doc->itemAddedAt], $doc->itemId);
};
MAP;

      $handler = new ViewHandler("perAddedAt");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(favoritesPerAddedAt());


    // @params: userId, type
    function favoritesPerAddedAtByType() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'star' && isset($doc->itemSupertype) && $doc->itemSupertype == 'post')
    $emit([$doc->userId, $doc->itemType, $doc->itemAddedAt], $doc->itemId);
};
MAP;

      $handler = new ViewHandler("perAddedAtByType");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(favoritesPerAddedAtByType());


    // @params: userId
    function favoritesPerPublishedAt() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'star' && isset($doc->itemSupertype) && $doc->itemSupertype == 'post' && $doc->index)
    $emit([$doc->userId, $doc->itemPublishedAt], $doc->itemId);
};
MAP;

      $handler = new ViewHandler("perPublishedAt");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(favoritesPerPublishedAt());


    // @params: userId, type
    function favoritesPerPublishedAtByType() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'star' && isset($doc->itemSupertype) && $doc->itemSupertype == 'post')
    $emit([$doc->userId, $doc->itemType, $doc->itemPublishedAt], $doc->itemId);
};
MAP;

      $handler = new ViewHandler("perPublishedAtByType");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(favoritesPerPublishedAtByType());


    $this->couch->saveDoc($doc);
  }


  protected function initUsers() {
    $doc = DesignDoc::create('users');


    // @params: [userId]
    function allUsers() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'user')
    $emit($doc->_id, [$doc->username, $doc->primaryEmail, $doc->creationDate]);
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
    $emit($doc->_id, [$doc->username, $doc->primaryEmail]);
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
  if ($doc->type == 'user') {
    foreach ($doc->emails as $email => $verified)
      $emit($email, $verified);
  }
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
    $emit([$doc->postId, $doc->publishedAt]);
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


  protected function initTest() {
    $doc = DesignDoc::create('test');


    function recentTags() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'post')
    $emit($doc->publishedAt, $doc->points);
};
MAP;

      $handler = new ViewHandler("recentTags");
      $handler->mapFn = $map;
      //$handler->reduceFn = $reduce;

      return $handler;
    }

    $doc->addHandler(recentTags());


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
      tags, votes, scores, stars, subscriptions, badges, favorites, users, reputation, replies.");
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

          case 'revisions':
            $this->initRevisions();
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

          case 'tests':
            $this->initTest();
            break;
        }

    }
    else
      $this->initAll();

    parent::execute($input, $output);
  }

}
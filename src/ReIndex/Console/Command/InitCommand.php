<?php

/*
 * @file InitCommand.php
 * @brief This file contains the InitCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

use EoC\Doc\DesignDoc;
use EoC\Handler\ViewHandler;


/**
 * @brief Initializes the ReIndex database, adding the required design documents.
 * @nosubgrouping
 */
class InitCommand extends AbstractCommand {

  protected $couch;


  private function printDone($name) {
    echo sprintf("%s... done", $name).PHP_EOL;
  }


  /**
   * @brief Returns a list of all the available design documents.
   * @return string
   */
  protected function listDocs() {
    return <<<'DOCS'
favorites
followers
friendships
members
posts
replies
reputation
revisions
stars
subscriptions
tags
tasks
updates
various
votes
DOCS;
  }


  /**
   * @brief Insert all design documents.
   */
  protected function initAll() {
    $this->initFavorites();
    $this->initFollowers();
    $this->initFriendships();
    $this->initMembers();
    $this->initPosts();
    $this->initReplies();
    $this->initReputation();
    $this->initRevisions();
    $this->initStars();
    $this->initSubscriptions();
    $this->initTags();
    $this->initTasks();
    $this->initUpdates();
    $this->initVarious();
    $this->initVotes();
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
        'state' => $doc->state,
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
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->state == 'current')
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
  if (isset($doc->supertype) && $doc->supertype == 'post' && ($doc->state == 'current' or $doc->state == 'deleted'))
    $emit([$doc->year, $doc->month, $doc->day, $doc->slug]);
};
MAP;

      $handler = new ViewHandler("byUrl");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(postsByUrl());


    // @params: id
    function postsByLegacyId() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->state == 'current')
    $emit($doc->legacyId);
};
MAP;

      $handler = new ViewHandler("byLegacyId");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(postsByLegacyId());


    // @params: year, month, day, slug
    function approvedRevisionsByUrl() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->state == 'approved')
    $emit([$doc->year, $doc->month, $doc->day, $doc->slug]);
};
MAP;

      $handler = new ViewHandler("approvedRevisionsByUrl");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(approvedRevisionsByUrl());


    function postsPerTag() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->state == 'current' && $doc->visible && isset($doc->tags))
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

    $this->printDone('posts');
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


    function newestTags() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'tag' && $doc->state == 'current')
    $emit($doc->createdAt);
};
MAP;

      $handler = new ViewHandler("newest");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(newestTags());


    function tagsAllNames() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'tag' && $doc->state == 'current')
    $emit($doc->unversionId, $doc->name);
};
MAP;

      $handler = new ViewHandler("allNames");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(tagsAllNames());


    function synonymsAllNames() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'synonym') {
    $emit($doc->_id, $doc->name);
  }
};
MAP;

      $handler = new ViewHandler("synonymsAllName");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(synonymsAllNames());


    function tagsByName() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'tag' && $doc->state == 'current' && empty($doc->synonymizing))
    $emit($doc->name);
};
MAP;

      $handler = new ViewHandler("byName");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(tagsByName());


    function synonymsByName() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'synonym') {
    $emit($doc->name);
  }
};
MAP;

      $handler = new ViewHandler("synonymsByName");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(synonymsByName());


    function tagsAndSynonymsByName() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (($doc->type == 'tag' && $doc->state == 'current') or $doc->type == 'synonym')
    $emit($doc->name);
};
MAP;

      $handler = new ViewHandler("andSynonymsByName");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(tagsAndSynonymsByName());


    function synonyms() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'tag') {
    $emit($doc->unversionId, $doc->unversionId);

    foreach ($doc->synonyms as $value)
      $emit($value, $doc->unversionId);
  }
};
MAP;

      $handler = new ViewHandler("synonyms");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(synonyms());


    function substrings() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'tag' && $doc->state == 'current') {
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


    $this->couch->saveDoc($doc);

    $this->printDone('tags');
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

    $this->printDone('revisions');
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


    // @params: [itemId, modifiedAt]
    function votesPerItemAndDate() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'vote')
    $emit([$doc->itemId, $doc->modifiedAt]);
};
MAP;

      $handler = new ViewHandler("perItemAndDate");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(votesPerItemAndDate());


    // @params: itemId, userId
    function votesPerItemAndMember() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'vote')
    $emit([$doc->itemId, $doc->userId], $doc->value);
};
MAP;

      $handler = new ViewHandler("perItemAndMember");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnSum(); // Used to count the votes.

      return $handler;
    }

    $doc->addHandler(votesPerItemAndMember());


    // @params: [userId]
    function votesPerMember() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'vote')
    $emit($doc->userId);
};
MAP;

      $handler = new ViewHandler("perMember");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(votesPerMember());


    $this->couch->saveDoc($doc);

    $this->printDone('votes');
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

    $this->printDone('stars');
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

    $this->printDone('subscriptions');
  }


  protected function initReputation() {
    $doc = DesignDoc::create('reputation');


    // @params userId, [timestamp]
    // @methods: Member.getReputation()
    function reputationPerMember() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'reputation')
    $emit([$doc->userId, $doc->timestamp], $doc->points);
};
MAP;

      $handler = new ViewHandler("perMember");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnSum();

      return $handler;
    }

    $doc->addHandler(reputationPerMember());


    $this->couch->saveDoc($doc);

    $this->printDone('reputation');
  }


  protected function initFavorites() {
    $doc = DesignDoc::create('favorites');


    // @params: userId
    function favoriteTagsPerMember() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'star' && $doc->itemType == 'tag')
    $emit($doc->userId, $doc->itemId);
};
MAP;

      $handler = new ViewHandler("tagsPerMember");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(favoriteTagsPerMember());


    // @params: userId
    function favoritesPerAddedAt() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'star' && $doc->itemSupertype == 'post')
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
  if ($doc->type == 'star' && $doc->itemSupertype == 'post')
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
  if ($doc->type == 'star' && $doc->itemSupertype == 'post')
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

    $this->printDone('favorites');
  }


  protected function initMembers() {
    $doc = DesignDoc::create('members');


    // @params: [userId]
    function allMembers() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'member')
    $emit($doc->_id, [$doc->username, $doc->primaryEmail, $doc->createdAt, $doc->firstName, $doc->lastName, $doc->headline]);
};
MAP;

      $handler = new ViewHandler("all");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(allMembers());


    // @params: [userId]
    function allMemberNames() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'member')
    $emit($doc->_id, [$doc->username, $doc->primaryEmail]);
};
MAP;

      $handler = new ViewHandler("allNames");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(allMemberNames());


    function newestMembers() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'member')
    $emit($doc->createdAt);
};
MAP;

      $handler = new ViewHandler("newest");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(newestMembers());


    function membersByUsername() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'member')
    $emit($doc->username, $doc->_id);
};
MAP;

      $handler = new ViewHandler("byUsername");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(membersByUsername());


    function membersByEmail() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'member') {
    foreach ($doc->emails as $email => $verified)
      $emit($email, $verified);
  }
};
MAP;

      $handler = new ViewHandler("byEmail");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(membersByEmail());


    function membersByConsumer() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'member') {
    foreach ($doc->logins as $loginName => $value)
      $emit($loginName);
  }
};
MAP;

      $handler = new ViewHandler("byConsumer");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(membersByConsumer());


    // @params: [postId]
    function membersHaveVoted() {
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

    $doc->addHandler(membersHaveVoted());


    $this->couch->saveDoc($doc);

    $this->printDone('members');
  }


  protected function initReplies() {
    $doc = DesignDoc::create('replies');


    // @params postId
    function repliesPerPost() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'reply')
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
  if ($doc->type == 'reply')
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
    function activeRepliesPerPost() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'reply')
    $emit([$doc->postId, $doc->modifiedAt]);
};
MAP;

      $handler = new ViewHandler("activePerPost");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(activeRepliesPerPost());


    $this->couch->saveDoc($doc);

    $this->printDone('replies');
  }


  protected function initFriendships() {
    $doc = DesignDoc::create('friendships');


    // @params: [userId]
    function friendshipsApprovedPerMember() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (($doc->type == 'friendship') && $doc->approved) {
    $emit([$doc->senderId, $doc->receiverId], $doc->approvedAt);
    $emit([$doc->receiverId, $doc->senderId], $doc->approvedAt);
  }
};
MAP;

      $handler = new ViewHandler("approvedPerMember");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(friendshipsApprovedPerMember());


    // @params: [userId]
    function pendingRequestsPerMember() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (($doc->type == 'friendship') && !$doc->approved) {
    $emit([$doc->senderId, $doc->receiverId], $doc->requestedAt);
    $emit([$doc->receiverId, $doc->senderId], $doc->requestedAt);
  }
};
MAP;

      $handler = new ViewHandler("pendingRequest");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(pendingRequestsPerMember());


    $this->couch->saveDoc($doc);

    $this->printDone('friendships');
  }


  protected function initFollowers() {
    $doc = DesignDoc::create('followers');


    // @params: [userId]
    function followersPerMember() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'follower') {
    $emit([$doc->memberId, $doc->followerId]);
  }
};
MAP;

      $handler = new ViewHandler("perMember");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(followersPerMember());


    // @params: [userId]
    function followingPerMember() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'follower') {
    $emit([$doc->followerId, $doc->memberId]);
  }
};
MAP;

      $handler = new ViewHandler("following");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(followingPerMember());


    $this->couch->saveDoc($doc);

    $this->printDone('followers');
  }


  protected function initTasks() {
    $doc = DesignDoc::create('tasks');


    function allTasks() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->tasks)) {
    foreach ($tasks as $key => $value)
      $emit($doc->_id, $key);
  }
};
MAP;

      $handler = new ViewHandler("all");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount();

      return $handler;
    }

    $doc->addHandler(allTasks());


    $this->couch->saveDoc($doc);

    $this->printDone('tasks');
  }


  protected function initUpdates() {
    $doc = DesignDoc::create('updates');


    // @params: postId
    function updatesPerDateByPostId() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if ($doc->type == 'comment')
    $emit([$doc->itemId, $doc->modifiedAt]);
  elseif (isset($doc->supertype) and $doc->supertype == 'reply')
    $emit([$doc->postId, $doc->modifiedAt]);
};
MAP;

      $handler = new ViewHandler("perDateByPostId");
      $handler->mapFn = $map;

      return $handler;
    }

    $doc->addHandler(updatesPerDateByPostId());


    $this->couch->saveDoc($doc);

    $this->printDone('updates');
  }


  protected function initVarious() {
    $doc = DesignDoc::create('various');


    // @params: NONE
    function postsPerDate() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->index && $doc->visible && $doc->state == 'current')
    $emit($doc->publishedAt);
};
MAP;

      $handler = new ViewHandler("postsPerDate");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(postsPerDate());


    // @params: type
    function postsPerDateByType() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->visible && $doc->state == 'current')
    $emit([$doc->type, $doc->publishedAt]);
};
MAP;

      $handler = new ViewHandler("postsPerDateByType");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(postsPerDateByType());


    // @params: NONE
    function postsPerDateByTag() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->index && $doc->visible && $doc->state == 'current' && isset($doc->tags))
    foreach ($doc->tags as $tagId)
      $emit([$tagId, $doc->publishedAt]);
};
MAP;

      $handler = new ViewHandler("postsPerDateByTag");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(postsPerDateByTag());


    // @params: type
    function postsPerDateByTagAndType() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->visible && $doc->state == 'current' && isset($doc->tags))
    foreach ($doc->tags as $tagId)
      $emit([$tagId, $doc->type, $doc->publishedAt]);
};
MAP;

      $handler = new ViewHandler("postsPerDateByTagAndType");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(postsPerDateByTagAndType());


    // @params: creatorId
    function postsPerDateByUser() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->index && $doc->visible && $doc->state == 'current')
    $emit([$doc->creatorId, $doc->publishedAt]);
};
MAP;

      $handler = new ViewHandler("postsPerDateByUser");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(postsPerDateByUser());


    // @params: creatorId, type
    function postsPerDateByUserAndType() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset($doc->supertype) && $doc->supertype == 'post' && $doc->visible && $doc->state == 'current')
    $emit([$doc->creatorId, $doc->type, $doc->publishedAt]);
};
MAP;

      $handler = new ViewHandler("postsPerDateByUserAndType");
      $handler->mapFn = $map;
      $handler->useBuiltInReduceFnCount(); // Used to count the posts.

      return $handler;
    }

    $doc->addHandler(postsPerDateByUserAndType());


    $this->couch->saveDoc($doc);

    $this->printDone('various');
  }


  protected function initTest() {
    $doc = DesignDoc::create('test');


    function recentTags() {
      $map = <<<'MAP'
function($doc) use ($emit) {
  if (isset(isset($doc->supertype) && $doc->supertype == 'post'))
    $emit($doc->publishedAt, $doc->points);
};
MAP;

      $handler = new ViewHandler("recentTags");
      $handler->mapFn = $map;

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
    $this->setDescription("Initializes the ReIndex database, adding the required design documents");

    $this->addArgument("documents",
      InputArgument::IS_ARRAY,
      <<<'DESC'
The design documents containing the views you want create
Separate multiple documents with a space. To obtain the complete list of available documents run the command with the option `--list`.
DESC
    );

    $this->addOption("list",
      "l",
      InputOption::VALUE_NONE,
      "Returns a list of available documents");
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->couch = $this->di['couchdb'];

    $documents = $input->getArgument('documents');

    if (empty($documents)) {
      if ($input->getOption('list'))
        print $this->listDocs() . PHP_EOL;
      else
        $this->initAll();
    }
    else {
      foreach ($documents as $name) {
        switch ($name) {
          case 'favorites':
            $this->initFavorites();
            echo sprintf("%s saved", $name) . PHP_EOL;
            break;

          case 'followers':
            $this->initFollowers();
            break;

          case 'friendships':
            $this->initFriendships();
            break;

          case 'members':
            $this->initMembers();
            break;

          case 'posts':
            $this->initPosts();
            break;

          case 'replies':
            $this->initReplies();
            break;

          case 'reputation':
            $this->initReputation();
            break;

          case 'revisions':
            $this->initRevisions();
            break;

          case 'stars':
            $this->initStars();
            break;

          case 'subscriptions':
            $this->initSubscriptions();
            break;

          case 'tags':
            $this->initTags();
            break;

          case 'tests':
            $this->initTest();
            break;

          case 'updates':
            $this->initUpdates();
            break;

          case 'various':
            $this->initVarious();
            break;

          case 'votes':
            $this->initVotes();
            break;

          default:
            echo sprintf("%s not found", $name) . PHP_EOL;
        }
      }
    }

    parent::execute($input, $output);
  }

}
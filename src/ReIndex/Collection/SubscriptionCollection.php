<?php

/**
 * @file SubscriptionCollection.php
 * @brief This file contains the SubscriptionCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Doc\Post;
use ReIndex\Doc\Member;
use ReIndex\Doc\Subscription;
use ReIndex\Helper\Text;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use Phalcon\Di;


final class SubscriptionCollection implements \Countable {

  /**
   * @var Di $di
   */
  protected $di;

  /**
   * @var Couch $couch
   */
  protected $couch;

  /**
   * @var Post $post
   */
  protected $post;


  /**
   * @brief Creates a new collection of items.
   */
  public function __construct(Post $post) {
    $this->di = Di::getDefault();
    $this->couch = $this->di['couchdb'];

    $this->post = $post;
  }


  public function count() {
    $opts = new ViewQueryOpts();
    $opts->setKey([$this->post->getUnversionId()]);

    return $this->couch->queryView("subscriptions", "perItem", NULL, $opts)->getReducedValue();
  }


  /**
   * @brief Returns `true` if the user has subscribed the current post.
   * @param[in] Member $member The current user logged in.
   * @param[in] string $subscriptionId (optional) The subscription document ID.
   * @retval boolean
   */
  public function exists(Member $member, &$subscriptionId = NULL) {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(1)->setKey([Text::unversion($this->post->id), $member->id]);

    $result = $this->couch->queryView("subscriptions", "perItem", NULL, $opts);

    if ($result->isEmpty())
      return FALSE;
    else {
      $subscriptionId = $result[0]['id'];
      return TRUE;
    }
  }


  public function alter(Member $member) {
    if (!$this->exists($member)) {
      $doc = Subscription::create($this->post, $member);
      $this->couch->saveDoc($doc);
    }
  }


  public function remove(Member $member) {
    $subscriptionId = NULL;

    if ($this->exists($member, $subscriptionId)) {
      $doc = $this->couch->getDoc(Couch::STD_DOC_PATH, $subscriptionId);
      $doc->delete();
      $this->couch->saveDoc($doc);
    }
  }

}
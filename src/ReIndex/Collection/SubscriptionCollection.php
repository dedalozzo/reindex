<?php

/**
 * @file SubscriptionCollection.php
 * @brief This file contains the SubscriptionCollection class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Collection;


use ReIndex\Feature\Subscribable;
use ReIndex\Model\Post;
use ReIndex\Model\Member;
use ReIndex\Model\Subscription;
use ReIndex\Helper\Text;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use Phalcon\Di;


class SubscriptionCollection implements \Countable {

  protected $di;    // Stores the default Dependency Injector.
  protected $post;  // Stores the current user.
  protected $couch; // Stores the CouchDB instance.


  /**
   * @brief Creates a new collection of items.
   */
  public function __construct(Post $post) {
    $this->post = $post;
    $this->di = Di::getDefault();
    $this->couch = $this->di['couchdb'];
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
      $doc = Subscription::create(Text::unversion($this->post->id), $member->id);
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
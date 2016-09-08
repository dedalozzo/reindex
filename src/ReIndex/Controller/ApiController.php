<?php

/**
 * @file ApiController.php
 * @brief This file contains the ApiController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Controller;


use EoC\Couch;


/**
 * @brief Controller for the API requests.
 * @nosubgrouping
 */
final class ApiController extends BaseController {


  /**
   * @brief Extracts the domain name.
   * @attention This method works, but we no londer use Cross-site HTTP requests.
   * @param[in] $url The URL.
   * @retval string
   */
  protected function getDomainName($url) {
    $pieces = parse_url($url);
    $domain = isset($pieces['host']) ? $pieces['host'] : '';

    if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $matches)) {
      return $matches['domain'];
    }
    else
      return "";
  }


  /**
   * @brief Cross-site HTTP requests are HTTP requests for resources from a different domain than the domain of the
   * resource making the request. This occurs very commonly on the web today, since pages load a number of resources in
   * a cross-site manner, including CSS stylesheets, images and scripts, and other resources.\n
   * Cross-site HTTP requests initiated from within scripts have been subject to well-known restrictions, for
   * well-understood security reasons.  For example HTTP Requests made using the XMLHttpRequest object were subject to
   * the same-origin policy.  In particular, this meant that a web application using XMLHttpRequest could only make
   * HTTP requests to the domain it was loaded from, and not to other domains.  Developers expressed the desire to
   * safely evolve capabilities such as XMLHttpRequest to make cross-site requests, for better, safer mash-ups within
   * web applications.\n
   * To make possible cross-site AJAX calls, for example from www.programmazione.it to api.programmazione.it, we
   * must set `Access-Control-Allow-Origin` header.
   * @attention This method works, but we no londer use Cross-site HTTP requests.
   */
  protected function validateOrigin() {

    if (isset($_SERVER['HTTP_ORIGIN'])) {
      $origin = $_SERVER['HTTP_ORIGIN'];

      if ($this->getDomainName($origin) == $this->domainName) {
        $this->response->setHeader('Access-Control-Allow-Origin', $origin);
        $this->response->setHeader('Access-Control-Allow-Credentials', 'true');
      }
      else
        throw new \DomainException("Stai tentando di effettuare una richiesta da un dominio sconosciuto.");
    }

  }


  /**
   * @brief Executes the specified action on a post.
   * @param[in] string $name The method name.
   */
  protected function fireMethod($name) {
    try {
      if ($this->request->hasPost('id')) {
        $doc = $this->couchdb->getDoc('posts', Couch::STD_DOC_PATH, $this->request->getPost('id'));
        call_user_func([$doc, $name]);
        echo json_encode([TRUE, $this->user->username, time()]);
        $this->view->disable();
      }
      else
        throw new \RuntimeException("La risorsa non è più disponibile.");
    }
    catch (\Exception $e) {
      echo json_encode([FALSE, $e->getMessage()]);
    }
  }


  /**
   * @brief Likes a document.
   * @param[in] string $dbName The database name where the document is stored.
   */
  protected function like($dbName) {
    try {
      if ($this->request->hasPost('id')) {
        $doc = $this->couchdb->getDoc($dbName, Couch::STD_DOC_PATH, $this->request->getPost('id'));
        echo json_encode([TRUE, $doc->like()]);
        $this->view->disable();
      }
      else
        throw new \RuntimeException("La risorsa non è più disponibile.");
    }
    catch (\Exception $e) {
      echo json_encode([FALSE, $e->getMessage()]);
    }
  }


  public function initialize() {
    parent::initialize();

    // DON'T CHANGE THIS.
    // We no longer validate the origin because we use the same domain.
    //$this->validateOrigin();
  }


  /**
   * @brief Adds a friend.
   */
  public function addFriendAction() {
    try {
      if ($this->request->hasPost('id')) {
        $member = $this->couchdb->getDoc('members', Couch::STD_DOC_PATH, $this->request->getPost('id'));
        $this->user->friends->add($member);
        echo json_encode([TRUE]);
        $this->view->disable();
      }
      else
        throw new \RuntimeException("La risorsa non è più disponibile.");
    }
    catch (\Exception $e) {
      echo json_encode([FALSE, $e->getMessage()]);
    }
  }


  /**
   * @brief Removes the friend.
   */
  public function removeFriendAction() {
    try {
      if ($this->request->hasPost('id')) {
        $member = $this->couchdb->getDoc('members', Couch::STD_DOC_PATH, $this->request->getPost('id'));
        $this->user->friends->remove($member);
        echo json_encode([TRUE]);
        $this->view->disable();
      }
      else
        throw new \RuntimeException("La risorsa non è più disponibile.");
    }
    catch (\Exception $e) {
      echo json_encode([FALSE, $e->getMessage()]);
    }
  }


  /**
   * @brief Likes a post.
   */
  public function likePostAction() {
    $this->like('posts');
  }


  /**
   * @brief Closes the post.
   */
  public function closePostAction() {
    $this->fireMethod('close');
  }


  /**
   * @brief Locks the post.
   */
  public function lockPostAction() {
    $this->fireMethod('lock');
  }


  /**
   * @brief Unprotects the post.
   */
  public function unprotectPostAction() {
    $this->fireMethod('removeProtection');
  }


  /**
   * @brief Moves the document to trash.
   */
  public function movePostToTrashAction() {
    $this->fireMethod('moveToTrash');
  }


  /**
   * @brief Restores the document.
   */
  public function restorePostAction() {
    $this->fireMethod('restore');
  }


  /**
   * @brief Likes a reply.
   */
  public function likeReplyAction() {
    $this->like('replies');
  }


  /**
   * @brief Adds a tag to the user's favorites.
   * @retval array
   */
  public function starTagAction() {
    try {
      if ($this->request->hasPost('id')) {
        $doc = $this->couchdb->getDoc('tags', Couch::STD_DOC_PATH, $this->request->getPost('id'));
        echo json_encode([TRUE, $doc->star()]);
        $this->view->disable();
      }
      else
        throw new \RuntimeException("La risorsa non è più disponibile.");
    }
    catch (\Exception $e) {
      echo json_encode([FALSE, $e->getMessage()]);
    }
  }

}
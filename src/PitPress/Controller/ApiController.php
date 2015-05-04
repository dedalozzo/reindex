<?php

/**
 * @file ApiController.php
 * @brief This file contains the ApiController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Controller;


use EoC\Couch;

use PitPress\Exception;


/**
 * @brief Controller for the API requests.
 * @nosubgrouping
 */
class ApiController extends BaseController {


  /**
   * @brief Extracts the domain name.
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


  public function initialize() {
    parent::initialize();

    $this->validateOrigin();
  }


  /**
   * @brief Likes a post.
   * @retval int
   */
  public function likeAction() {
    try {
      if ($this->request->hasPost('id')) {
        $doc = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $this->request->getPost('id'));
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


  /**
   * @brief Stars an item.
   * @retval int
   */
  public function starAction() {
    try {
      if ($this->request->hasPost('id')) {
        $doc = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $this->request->getPost('id'));
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


  /**
   * @brief Submits a versionable document.
   * @retval int
   */
  public function submitAction() {

  }


  /**
   * @brief Approves a versionable document.
   * @retval int
   */
  public function approveAction() {

  }


  /**
   * @brief Returns for revision a versionable document.
   * @retval int
   */
  public function returnForRevisionAction() {

  }


  /**
   * @brief Rejects a versionable document.
   * @retval int
   */
  public function rejectAction() {

  }


  /**
   * @brief Reverts a versionable document.
   * @retval int
   */
  public function revertAction() {

  }


  /**
   * @brief Moves the document to trash.
   * @retval int
   */
  public function moveToTrashAction() {
    try {
      if ($this->request->hasPost('id')) {
        $doc = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $this->request->getPost('id'));

        if ($doc->canBeMovedToTrash()) {
          $doc->moveToTrash();
          $doc->save();
          echo json_encode([TRUE, $this->user->username, time()]);
          $this->view->disable();
        }
        else
          throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");
      }
      else
        throw new \RuntimeException("La risorsa non è più disponibile.");
    }
    catch (\Exception $e) {
      echo json_encode([FALSE, $e->getMessage()]);
    }
  }


  /**
   * @brief Restores the document.
   * @retval int
   */
  public function restoreAction() {
    try {
      if ($this->request->hasPost('id')) {
        $doc = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $this->request->getPost('id'));

        if ($doc->canBeRestored()) {
          $doc->restore();
          $doc->save();
          echo json_encode([TRUE]);
          $this->view->disable();
        }
        else
          throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");
      }
      else
        throw new \RuntimeException("La risorsa non è più disponibile.");
    }
    catch (\Exception $e) {
      echo json_encode([FALSE, $e->getMessage()]);
    }
  }


  /**
   * @brief Mark as draft a versionable document.
   * @retval int
   */
  public function markAsDraftAction() {

  }


  /**
   * @brief Closes the post.
   * @retval int
   */
  public function closeAction() {
    try {
      if ($this->request->hasPost('id')) {
        $doc = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $this->request->getPost('id'));

        if ($doc->canBeProtected()) {
          $doc->close();
          $doc->save();
          echo json_encode([TRUE, $this->user->username, time()]);
          $this->view->disable();
        }
        else
          throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");
      }
      else
        throw new \RuntimeException("La risorsa non è più disponibile.");
    }
    catch (\Exception $e) {
      echo json_encode([FALSE, $e->getMessage()]);
    }
  }


  /**
   * @brief Locks the post.
   * @retval int
   */
  public function lockAction() {
    try {
      if ($this->request->hasPost('id')) {
        $doc = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $this->request->getPost('id'));

        if ($doc->canBeProtected()) {
          $doc->lock();
          $doc->save();
          echo json_encode([TRUE, $this->user->username, time()]);
          $this->view->disable();
        }
        else
          throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");
      }
      else
        throw new \RuntimeException("La risorsa non è più disponibile.");
    }
    catch (\Exception $e) {
      echo json_encode([FALSE, $e->getMessage()]);
    }
  }


  /**
   * @brief Unprotects the post.
   * @retval int
   */
  public function unprotectAction() {
    try {
      if ($this->request->hasPost('id')) {
        $doc = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $this->request->getPost('id'));

        if ($doc->canBeUnprotected()) {
          $doc->unprotect();
          $doc->save();
          echo json_encode([TRUE, $this->user->username, time()]);
          $this->view->disable();
        }
        else
          throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");
      }
      else
        throw new \RuntimeException("La risorsa non è più disponibile.");
    }
    catch (\Exception $e) {
      echo json_encode([FALSE, $e->getMessage()]);
    }
  }


  /**
   * @brief Hides the post.
   * @retval int
   */
  public function hideAction() {
    try {
      if ($this->request->hasPost('id')) {
        $doc = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $this->request->getPost('id'));

        if ($doc->canVisibilityBeChanged()) {
          $doc->hide();
          $doc->save();
          echo json_encode([TRUE, $this->user->username, time()]);
          $this->view->disable();
        }
        else
          throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");
      }
      else
        throw new \RuntimeException("La risorsa non è più disponibile.");
    }
    catch (\Exception $e) {
      echo json_encode([FALSE, $e->getMessage()]);
    }
  }


  /**
   * @brief Shows the post.
   * @retval int
   */
  public function showAction() {
    try {
      if ($this->request->hasPost('id')) {
        $doc = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $this->request->getPost('id'));

        if ($doc->canVisibilityBeChanged()) {
          $doc->show();
          $doc->save();
          echo json_encode([TRUE, $this->user->username, time()]);
          $this->view->disable();
        }
        else
          throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");
      }
      else
        throw new \RuntimeException("La risorsa non è più disponibile.");
    }
    catch (\Exception $e) {
      echo json_encode([FALSE, $e->getMessage()]);
    }
  }

}
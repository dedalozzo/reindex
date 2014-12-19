<?php

//! @file AjaxController.php
//! @brief This file contains the AjaxController class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use ElephantOnCouch\Couch;


/**
 * @brief Controller for the AJAX requests.
 * @nosubgrouping
 */
class AjaxController extends BaseController {


  /**
   * @brief Extracts the domain name.
   * @param[in] $url The URL.
   * @return string
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
   * To make possible cross-site AJAX calls, for example from blog.programmazione.it to ajax.programmazione.it, we
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
   * @return int
   */
  public function likeAction() {
    try {
      if ($this->request->hasPost('id')) {
        $doc = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $this->request->getPost('id'));
        echo json_encode($doc->like());

        $this->view->disable();
      }
      else
        throw new \RuntimeException("La risorsa non è più disponibile.");
    }
    catch (\Exception $e) {
      echo json_encode($e->getMessage());
    }
  }


  /**
   * @brief Stars an item.
   * @return int
   */
  public function starAction() {
    try {
      if ($this->request->hasPost('id')) {
        $doc = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $this->request->getPost('id'));
        echo json_encode($doc->star());

        $this->view->disable();
      }
      else
        throw new \RuntimeException("La risorsa non è più disponibile.");
    }
    catch (\Exception $e) {
      echo json_encode($e->getMessage());
    }
  }


  /**
   * @brief Moves the document to trash.
   * @return int
   */
  public function moveToTrashAction() {
    try {
      if ($this->request->hasPost('id')) {
        $doc = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $this->request->getPost('id'));
        echo json_encode($doc->moveToTrash());
        $doc->save();

        $this->view->disable();
      } else
        throw new \RuntimeException("La risorsa non è più disponibile.");
    }
    catch (\Exception $e) {
      echo json_encode($e->getMessage());
    }
  }


  /**
   * @brief Restores the document.
   * @return int
   */
  public function restoreAction() {
    try {
      if ($this->request->hasPost('id')) {
        $doc = $this->couchdb->getDoc(Couch::STD_DOC_PATH, $this->request->getPost('id'));
        echo json_encode($doc->restore());
        $doc->save();

        $this->view->disable();
      } else
        throw new \RuntimeException("La risorsa non è più disponibile.");
    }
    catch (\Exception $e) {
      echo json_encode($e->getMessage());
    }
  }

}
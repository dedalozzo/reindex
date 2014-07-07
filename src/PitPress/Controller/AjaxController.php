<?php

//! @file AjaxController.php
//! @brief This file contains the AjaxController class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


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


  public function afterExecuteRoute() {
    parent::afterExecuteRoute();

    /*
     * Cross-site HTTP requests are HTTP requests for resources from a different domain than the domain of the resource
     * making the request.  For instance, a resource loaded from Domain A (http://domaina.example) such as an HTML web
     * page, makes a request for a resource on Domain B (http://domainb.foo), such as an image, using the img element
     * (http://domainb.foo/image.jpg).  This occurs very commonly on the web today — pages load a number of resources in
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
    if (isset($_SERVER['HTTP_ORIGIN'])) {
      $origin = $_SERVER['HTTP_ORIGIN'];

      if ($this->getDomainName($origin) == $this->domainName)
        $this->response->setHeader('Access-Control-Allow-Origin', $origin);
    }
  }

}
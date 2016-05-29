<?php

/**
 * @file ListController.php
 * @brief This file contains the ListController class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Controller;


use ReIndex\Helper;

use Phalcon\Mvc\View;


/*
 * @brief Ancestor controller for any controller displaying list of entries: posts, members, etc..
 * @nosubgrouping
 */
abstract class ListController extends BaseController {

  /**
   * @var int $resultsPerPage
   */
  protected $resultsPerPage;


  /**
   * @brief Builds the pagination url for CouchDB.
   * @param[in] mixed $startKey A key.
   * @param[in] string $startKeyDocId A document ID.
   * @retval string The pagination url.
   */
  protected function buildPaginationUrlForCouch($startKey, $startKeyDocId) {
    return sprintf('%s%s?startkey=%s&startkey_docid=%s', $this->domainName, parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), $startKey, $startKeyDocId);
  }


  /**
   * @brief Builds the pagination url for Redis.
   * @param[in] int $offset The offset.
   * @retval string The pagination url.
   */
  protected function buildPaginationUrlForRedis($offset) {
    return sprintf('%s%s?offset=%d', $this->domainName, parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), $offset);
  }


  public function initialize() {
    // Prevents to call the method twice in case of forwarding.
    if ($this->dispatcher->isFinished() && $this->dispatcher->wasForwarded())
      return;

    parent::initialize();
  }


  public function afterExecuteRoute() {
    // Prevents to call the method twice in case of forwarding.
    if ($this->dispatcher->isFinished() && $this->dispatcher->wasForwarded())
      return;

    parent::afterExecuteRoute();
  }

}
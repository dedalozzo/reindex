<?php

//! @file Stat.php
//! @brief This file contains the Stat class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Helper;


use Phalcon\DI;


//! @brief Provides methods to generate statistics.
class Stat {
  protected $di; // Stores the default Dependency Injector.
  protected $couch; // Stores the ElephantOnCouch client instance.
  protected $redis; // Stores the Redis client instance.


  //! @brief Constructor.
  public function __construct() {
    $this->di = DI::getDefault();
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];
  }


  //! @brief Gets the total number of updates.
  public function getUpdatesCount() {
    $result = $this->couch->queryView("posts", "allLatest");

    if (empty($result['rows']))
      return 0;
    else
      return number_format($result['rows'][0]['value'], 0, ",", ".");
  }


  //! @brief Gets the total number of blog entries.
  public function getBlogEntriesCount() {

  }


  //! @brief Gets the total number of articles.
  public function getArticlesCount() {

  }


  //! @brief Gets the total number of books.
  public function getBooksCount() {

  }


  //! @brief Gets the total number of links.
  public function getLinksCount() {

  }


  //! @brief Gets the total number of questions.
  public function getQuestionsCount() {

  }

} 
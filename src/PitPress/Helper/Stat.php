<?php

//! @file Stat.php
//! @brief This file contains the Stat class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Helper;


use Phalcon\DI;

use ElephantOnCouch\Opt\ViewQueryOpts;


//! @brief Provides methods to generate statistics.
class Stat {
  protected $di; // Stores the default Dependency Injector.
  protected $couch; // Stores the ElephantOnCouch client instance.


  //! @brief Constructor.
  public function __construct() {
    $this->di = DI::getDefault();
    $this->couch = $this->di['couchdb'];
  }


  private function formatNumber($rows) {
    if (empty($rows))
      return 0;
    else
      return number_format($rows[0]['value'], 0, ",", ".");
  }


  //! @brief Gets the total number of updates.
  public function getUpdatesCount() {
    $rows = $this->couch->queryView("posts", "newest")['rows'];
    return $this->formatNumber($rows);
  }


  //! @brief Gets the total number of blog entries.
  public function getBlogEntriesCount() {
    $opts = new ViewQueryOpts();
    $opts->setStartKey(['blog'])->setEndKey(['blog', new \stdClass()]);
    $rows = $this->couch->queryView('posts', 'newestPerSection', NULL, $opts)['rows'];
    return $this->formatNumber($rows);
  }


  //! @brief Gets the total number of articles.
  public function getArticlesCount() {
    $opts = new ViewQueryOpts();
    $opts->setStartKey(['article'])->setEndKey(['article', new \stdClass()]);
    $rows = $this->couch->queryView('posts', 'newestPerType', NULL, $opts)['rows'];
    return $this->formatNumber($rows);
  }


  //! @brief Gets the total number of books.
  public function getBooksCount() {
    $opts = new ViewQueryOpts();
    $opts->setStartKey(['book'])->setEndKey(['book', new \stdClass()]);
    $rows = $this->couch->queryView('posts', 'newestPerType', NULL, $opts)['rows'];
    return $this->formatNumber($rows);
  }


  //! @brief Gets the total number of tutorials.
  public function getTutorialsCount() {
    $opts = new ViewQueryOpts();
    $opts->setStartKey(['tutorial'])->setEndKey(['tutorial', new \stdClass()]);
    $rows = $this->couch->queryView('posts', 'newestPerType', NULL, $opts)['rows'];
    return $this->formatNumber($rows);
  }


  //! @brief Gets the total number of links.
  public function getLinksCount() {
    $opts = new ViewQueryOpts();
    $opts->setStartKey(['link'])->setEndKey(['link', new \stdClass()]);
    $rows = $this->couch->queryView('posts', 'newestPerType', NULL, $opts)['rows'];
    return $this->formatNumber($rows);
  }


  //! @brief Gets the total number of questions.
  public function getQuestionsCount() {
    $opts = new ViewQueryOpts();
    $opts->setStartKey(['question'])->setEndKey(['question', new \stdClass()]);
    $rows = $this->couch->queryView('posts', 'newestPerType', NULL, $opts)['rows'];
    return $this->formatNumber($rows);
  }


  //! @brief Gets the total number of tags.
  public function getTagsCount() {
    $rows = $this->couch->queryView("tags", "all")['rows'];
    return $this->formatNumber($rows);
  }

}
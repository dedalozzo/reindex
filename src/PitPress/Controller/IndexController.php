<?php

//! @file IndexController.php
//! @brief This file contains the IndexController class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Helper\Stat;
use PitPress\Helper\Time;


//! @brief Controller of Index actions.
//! @nosubgrouping
class IndexController extends ListController {


  public function indexAction() {
    $this->newestAction();
    $this->view->title = self::TITLE;
  }


  public function notFoundAction() {
    $this->response->setHeader(404 , 'Not Found');
    $this->view->pick('404/404');
  }


  public function newestAction() {
    $this->view->title = "Ultimi aggiornamenti - ".self::TITLE;

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults();
    $opts->setLimit(30);
    $rows = $this->couch->queryView("posts", "latest", NULL, $opts)['rows'];

    // Entries.
    $keys = array_column($rows, 'id');
    $this->view->entries = $this->getEntries($keys);

    // Stats.
    $this->view->stat = new Stat();

    // Recent tags.
    $this->view->recentTags = $this->getRecentTags();
  }


  public function dailyPopularAction() {
    $this->view->title = "Aggiornamenti popolari - ".self::TITLE;

    // Retrieves the post of the last 7 days.
    /*$opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults();

    $aWeekAgo = (string)strtotime("-1 week");
    $opts->setStartKey($aWeekAgo);

    $result = $this->couch->queryView("index", "latest", $opts);

    $posts = [];*/
    /*foreach ($result["rows"] as $row)
      $this->redis->hGet($row->id)


    $posts[] = $this->couch->getDoc(Couch::STD_DOC_PATH, $row["id"], NULL, $docOpts);

    $this->view->setVar("posts", $posts);*/


    // PREVIOUS VERSION, BEFORE INCLUDE MISSING ROWS
    /*
    // Posts.
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults();
    $opts->setLimit(30);
    $result = $this->couch->queryView("posts", "allLatest", NULL, $opts);

    $posts = [];
    foreach ($result["rows"] as $row) {
      $doc = $this->couch->getDoc(Couch::STD_DOC_PATH, $row["id"], NULL);
      $posts[] = $doc;
    }
    */
  }


  public function weeklyPopularAction() {
  }


  public function monthlyPopularAction() {
  }


  public function yearlyPopularAction() {
    
  }
  
  
  public function everPopularAction() {    
  }


  public function weeklyActiveAction() {
  }


  public function monthlyActiveAction() {
  }


  public function yearlyActiveAction() {

  }


  public function everActiveAction() {
  }


  public function rssAction() {
  }

}
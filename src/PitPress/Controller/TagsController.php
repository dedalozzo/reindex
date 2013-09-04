<?php

//! @file TagsController.php
//! @brief Controller of Tags actions.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use ElephantOnCouch\Opt\ViewQueryOpts;


//! @brief Controller of Tags actions.
//! @nosubgrouping
class TagsController extends BaseController {

  protected static $controllerPath = '/tags/';
  protected static $controllerIndex = 4;
  protected static $controllerLabel = 'TAGS';

  // Stores the main menu definition.
  protected static $actionMenu = [
    ['link' => 'sinonimi/', 'name' => 'SINONIMI'],
    ['link' => 'nuovi/', 'name' => 'NUOVI'],
    ['link' => 'per-nome/', 'name' => 'PER NOME'],
    ['link' => 'popolari/', 'name' => 'POPOLARI']
  ];


  //! Displays the index.
  public function indexAction() {
    $this->popularAction();
  }

  
  //! @brief Displays the most popular tags.
  public function popularAction() {
    $this->view->sectionIndex = 3;
    $this->view->title = "Tags popolari";
  }


  //! @brief Displays the tags sorted by name.
  public function byNameAction() {
    $this->view->sectionIndex = 2;
    $this->view->title = "Tags in ordine alfabetico";
  }


  //! @brief Displays the newest tags.
  public function newestAction() {
    $this->view->sectionIndex = 1;
    $this->view->title = "Nuovi tags";

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults();
    $opts->setLimit(30);
    $rows = $this->couch->queryView("tags", "latest", NULL, $opts)['rows'];

    // Entries.
    $keys = array_column($rows, 'id');
    $this->view->entries = $this->getEntries($keys);
  }


  //! @brief Displays the synonyms.
  public function synonymsAction() {
    $this->view->sectionIndex = 0;
    $this->view->title = "Sinonimi";
  }


}
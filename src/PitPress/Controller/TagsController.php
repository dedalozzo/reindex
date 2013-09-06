<?php

//! @file TagsController.php
//! @brief Controller of Tags actions.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use ElephantOnCouch\Opt\ViewQueryOpts;


//! @brief Controller of Tags actions.
//! @nosubgrouping
class TagsController extends ListController {

  protected static $sectionLabel = 'TAGS';

  // Stores the main menu definition.
  protected static $sectionMenu = [
    ['name' => 'synonyms', 'link' => 'sinonimi/', 'label' => 'SINONIMI', 'title' => 'Sinonimi'],
    ['name' => 'newest', 'link' => 'nuovi/', 'label' => 'NUOVI', 'title' => 'Nuovi tags'],
    ['name' => 'byName', 'link' => 'per-nome/', 'label' => 'PER NOME', 'title' => 'Tags in ordine alfabetico'],
    ['name' => 'popular', 'link' => 'popolari/', 'label' => 'POPOLARI', 'title' => 'Tags popolari']
  ];


  //! Displays the index.
  public function indexAction() {
    $this->popularAction();
  }

  
  //! @brief Displays the most popular tags.
  public function popularAction() {
  }


  //! @brief Displays the tags sorted by name.
  public function byNameAction() {
  }


  //! @brief Displays the newest tags.
  public function newestAction() {
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->reverseOrderOfResults();
    $opts->setLimit(30);
    $rows = $this->couch->queryView("tags", "latest", NULL, $opts)['rows'];

    // Entries.
    $keys = array_column($rows, 'id');
    $this->view->setVar('entries', $this->getEntries($keys));
  }


  //! @brief Displays the synonyms.
  public function synonymsAction() {
  }


}
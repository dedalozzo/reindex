<?php

//! @file Article.php
//! @brief This file contains the Article class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model;


class Article extends Item {

  // Overrides the Item implementation, because an article doesn't have a name but a title.
  public function getName() {
    throw new \BadMethodCallException();
  }


  // Overrides the Item implementation, because an article doesn't have a name but a title.
  public function issetName() {
    throw new \BadMethodCallException();
  }


  // Overrides the Item implementation, because an article doesn't have a name but a title.
  public function setName($value) {
    throw new \BadMethodCallException();
  }


  // Overrides the Item implementation, because an article doesn't have a name but a title.
  public function unsetName() {
    throw new \BadMethodCallException();
  }

}

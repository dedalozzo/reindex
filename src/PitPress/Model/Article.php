<?php

//! @file Article.php
//! @brief This file contains the Article class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model;


//! @brief
//! @nosubgrouping
class Article extends Item {

  //! @name Item's Attributes
  //! @brief Those are standard item's attributes.
  //@{
  const TITLE = "title"; //!< Document's title.
  //@}


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


  public function getTitle() {
    return $this->meta[self::TITLE];
  }

  public function issetTitle() {
    return isset($this->meta[self::TITLE]);
  }


  public function setTitle($value) {
    $this->meta[self::TITLE] = $value;
  }

  public function unsetTitle() {
    if ($this->isMetadataPresent(self::TITLE))
      unset($this->meta[self::TITLE]);
  }

}
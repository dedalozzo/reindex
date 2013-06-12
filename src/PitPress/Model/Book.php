<?php

//! @file Boook.php
//! @brief This file contains the Book class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model;


//! @brief
//! @nosubgrouping
class Book extends Article {

  //! @name Book's Attributes
  //! @brief Those are standard book's attributes.
  //@{
  const ISBN = "isbn"; //!< Book's ISBN.
  const AUTHORS = "authors"; //!< Book's authors.
  const PUBLISHER = "publisher"; //!< Book's publisher.
  const LANGUAGE = "language"; //!< Book's language.
  const YEAR = "year"; //!< Book's publishing year.
  const PAGES = "pages"; //!< Book's pages.
  const ATTACHMENTS = "attachments"; //!< Book's attachments.
  const POSITIVE = "positive"; //!< Book's positive aspects.
  const NEGATIVE = "negative"; //!< Book's negative aspects.
  const COVER = "cover"; //!< Book's cover.
  //@}


  public function getIsbn() {
    return $this->meta[self::ISBN];
  }


  public function issetIsbn() {
    return isset($this->meta[self::ISBN]);
  }


  public function setIsbn($value) {
    $this->meta[self::ISBN] = $value;
  }

  public function unsetIsbn() {
    if ($this->isMetadataPresent(self::ISBN))
      unset($this->meta[self::ISBN]);
  }


  public function getAuthors() {
    return $this->meta[self::AUTHORS];
  }


  public function issetAuthors() {
    return isset($this->meta[self::AUTHORS]);
  }


  public function setAuthors($value) {
    $this->meta[self::AUTHORS] = $value;
  }


  public function unsetAuthors() {
    if ($this->isMetadataPresent(self::AUTHORS))
      unset($this->meta[self::AUTHORS]);
  }


  public function getPublisher($value) {
    $this->meta[self::PUBLISHER] = $value;
  }


  public function issetPublisher() {
    return isset($this->meta[self::PUBLISHER]);
  }


  public function setPublisher() {
    return $this->meta[self::PUBLISHER];
  }


  public function unsetPublisher() {
    if ($this->isMetadataPresent(self::PUBLISHER))
      unset($this->meta[self::PUBLISHER]);
  }


  public function getLanguage() {
    return $this->meta[self::LANGUAGE];
  }


  public function issetLanguage() {
    return isset($this->meta[self::LANGUAGE]);
  }


  public function setLanguage($value) {
    $this->meta[self::LANGUAGE] = $value;
  }


  public function unsetLanguage() {
    if ($this->isMetadataPresent(self::LANGUAGE))
      unset($this->meta[self::LANGUAGE]);
  }


  public function getYear() {
    return $this->meta[self::YEAR];
  }


  public function issetYear() {
    return isset($this->meta[self::YEAR]);
  }


  public function setYear($value) {
    $this->meta[self::YEAR] = $value;
  }


  public function unsetYear() {
    if ($this->isMetadataPresent(self::YEAR))
      unset($this->meta[self::YEAR]);
  }


  public function getPages() {
    return $this->meta[self::PAGES];
  }


  public function issetPages() {
    return isset($this->meta[self::PAGES]);
  }


  public function setPages($value) {
    $this->meta[self::PAGES] = $value;
  }


  public function unsetPages() {
    if ($this->isMetadataPresent(self::PAGES))
      unset($this->meta[self::PAGES]);
  }


  public function getAttachments() {
    return $this->meta[self::ATTACHMENTS];
  }


  public function issetAttachments() {
    return isset($this->meta[self::ATTACHMENTS]);
  }


  public function setAttachments($value) {
    $this->meta[self::ATTACHMENTS] = $value;
  }


  public function unsetAttachments() {
    if ($this->isMetadataPresent(self::ATTACHMENTS))
      unset($this->meta[self::ATTACHMENTS]);
  }


  public function getPositive() {
    return $this->meta[self::POSITIVE];
  }


  public function issetPositive() {
    return isset($this->meta[self::POSITIVE]);
  }


  public function setPositive($value) {
    $this->meta[self::POSITIVE] = $value;
  }


  public function unsetPositive() {
    if ($this->isMetadataPresent(self::POSITIVE))
      unset($this->meta[self::POSITIVE]);
  }


  public function getNegative() {
    return $this->meta[self::NEGATIVE];
  }


  public function issetNegative() {
    return isset($this->meta[self::NEGATIVE]);
  }


  public function setNegative($value) {
    $this->meta[self::NEGATIVE] = $value;
  }


  public function unsetNegative() {
    if ($this->isMetadataPresent(self::NEGATIVE))
      unset($this->meta[self::NEGATIVE]);
  }


  public function getCover() {
  }


  public function setCover($value) {
  }

}

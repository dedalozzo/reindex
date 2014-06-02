<?php

/**
 * @file Book.php
 * @brief This file contains the Book class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model\Blog;


/**
 * @brief This class represents a book review.
 * @nosubgrouping
 */
class Book extends Article {


  public function getPublishingType() {
    return 'LIBRO';
  }


  public function getCover() {
  }


  public function setCover($value) {
  }


  //! @cond HIDDEN_SYMBOLS

  public function getIsbn() {
    return $this->meta['isbn'];
  }


  public function issetIsbn() {
    return isset($this->meta['isbn']);
  }


  public function setIsbn($value) {
    $this->meta['isbn'] = $value;
  }


  public function unsetIsbn() {
    if ($this->isMetadataPresent('isbn'))
      unset($this->meta['isbn']);
  }


  public function getAuthors() {
    return $this->meta['authors'];
  }


  public function issetAuthors() {
    return isset($this->meta['authors']);
  }


  public function setAuthors($value) {
    $this->meta['authors'] = $value;
  }


  public function unsetAuthors() {
    if ($this->isMetadataPresent('authors'))
      unset($this->meta['authors']);
  }


  public function getPublisher() {
    return $this->meta['publisher'];
  }


  public function issetPublisher() {
    return isset($this->meta['publisher']);
  }


  public function setPublisher($value) {
    $this->meta['publisher'] = $value;
  }


  public function unsetPublisher() {
    if ($this->isMetadataPresent('publisher'))
      unset($this->meta['publisher']);
  }


  public function getLanguage() {
    return $this->meta['language'];
  }


  public function issetLanguage() {
    return isset($this->meta['language']);
  }


  public function setLanguage($value) {
    $this->meta['language'] = $value;
  }


  public function unsetLanguage() {
    if ($this->isMetadataPresent('language'))
      unset($this->meta['language']);
  }


  public function getYear() {
    return $this->meta['year'];
  }


  public function issetYear() {
    return isset($this->meta['year']);
  }


  public function setYear($value) {
    $this->meta['year'] = $value;
  }


  public function unsetYear() {
    if ($this->isMetadataPresent('year'))
      unset($this->meta['year']);
  }


  public function getPages() {
    return $this->meta['pages'];
  }


  public function issetPages() {
    return isset($this->meta['pages']);
  }


  public function setPages($value) {
    $this->meta['pages'] = $value;
  }


  public function unsetPages() {
    if ($this->isMetadataPresent('pages'))
      unset($this->meta['pages']);
  }


  public function getAttachments() {
    return $this->meta['attachments'];
  }


  public function issetAttachments() {
    return isset($this->meta['attachments']);
  }


  public function setAttachments($value) {
    $this->meta['attachments'] = $value;
  }


  public function unsetAttachments() {
    if ($this->isMetadataPresent('attachments'))
      unset($this->meta['attachments']);
  }


  public function getPositive() {
    return $this->meta['positive'];
  }


  public function issetPositive() {
    return isset($this->meta['positive']);
  }


  public function setPositive($value) {
    $this->meta['positive'] = $value;
  }


  public function unsetPositive() {
    if ($this->isMetadataPresent('positive'))
      unset($this->meta['positive']);
  }


  public function getNegative() {
    return $this->meta['negative'];
  }


  public function issetNegative() {
    return isset($this->meta['negative']);
  }


  public function setNegative($value) {
    $this->meta['negative'] = $value;
  }


  public function unsetNegative() {
    if ($this->isMetadataPresent('negative'))
      unset($this->meta['negative']);
  }


  public function getLink() {
    return $this->meta['link'];
  }


  public function issetLink() {
    return isset($this->meta['link']);
  }


  public function setLink($value) {
    $this->meta['link'] = $value;
  }


  public function unsetLink() {
    if ($this->isMetadataPresent('link'))
      unset($this->meta['link']);
  }

  //! @endcond

}

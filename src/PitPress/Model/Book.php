<?php

//! @file Boook.php
//! @brief This file contains the Book class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Model;


class Book extends Item {

  public function setIsbn($value) {
    $this->meta["isbn"] = $value;
  }


  public function getIsbn() {
    return $this->meta["isbn"];
  }


  public function setAuthors($value) {
    $this->meta["authors"] = $value;
  }


  public function getAuthors() {
    return $this->meta["authors"];
  }


  public function setPublisher() {
    return $this->meta["publisher"];
  }


  public function getPublisher($value) {
    $this->meta["publisher"] = $value;
  }


  public function setLanguage($value) {
    $this->meta["language"] = $value;
  }


  public function getLanguage() {
    return $this->meta["language"];
  }


  public function setPublishingYear($value) {
    $this->meta["year"] = $value;
  }


  public function getPublishingYear() {
    return $this->meta["year"];
  }


  public function setPages($value) {
    $this->meta["pages"] = $value;
  }


  public function getPages() {
    return $this->meta["pages"];
  }


  public function setAttachments($value) {
    $this->meta["attachments"] = $value;
  }


  public function getAttachments() {
    return $this->meta["attachments"];
  }


  public function setPositive($value) {
    $this->meta["positive"] = $value;
  }


  public function getPositive() {
    return $this->meta["positive"];
  }


  public function setNegative($value) {
    $this->meta["negative"] = $value;
  }


  public function getNegative() {
    return $this->meta["negative"];
  }


  public function setCover($value) {
  }


  public function getCover() {
  }

}

<?php

//! @file ImportTask.php
//! @brief
//! @details
//! @author Filippo F. Fadda


//! @brief
namespace PitPress\Task;


use Phalcon\CLI\Task;
use PitPress\Model\Article;
use PitPress\Model\Book;
use PitPress\Model\Tag;
use PitPress\Model\User;


//! @brief
//! @nosubgrouping
class ImportTask extends Task {

  const LIMIT = " LIMIT 500";

  const ARTICLE_DRAFT = 0;
  const ARTICLE = 2;
  const INFORMATIVE = 1;
  const ERROR = 3;
  const DOWNLOAD = 133;

  const BOOK_DRAFT = 10;
  const BOOK = 11;

  const DISCUSSION_DRAFT = 30;
  const DISCUSSION = 31;


  // Default action.
  public function mainAction() {
    echo "You must specify ";
  }


  //! @brief Imports all data.
  public function allAction() {
    $this->usersAction();
    $this->articlesAction();
    $this->booksAction();
    $this->tagsAction();
  }


  //! @brief Imports users.
  public function usersAction() {
    $sql = "SELECT idMember, name AS firstName, surname AS lastName, nickName AS displayName, email, password, sex, birthDate AS birthday, ipAddress, confirmHash AS confirmationHash, confirmed AS authenticated, regDate AS creationDate, lastUpdate, avatarData, avatarType, realNamePcy, passed FROM Memmer";

    $result = mysql_query($sql, $connection) or die(mysql_error());

    while ($item = mysql_fetch_object($result)) {
      $item->firstName = utf8_encode($item->firstName);
      $item->lastName = utf8_encode($item->lastName);
      $item->displayName = utf8_encode($item->displayName);
      $item->email = utf8_encode($item->email);
      $item->password = utf8_encode($item->password);
      $item->confirmationHash = utf8_encode($item->confirmationHash);

      $doc = new User;
      //$doc->docClass = '\PitPress\Model\Category';
      $doc->assignObject($item);

      $couch->saveDoc($doc);
    }

    mysql_free_result($result);

  }


  //! @brief Imports articles.
  public function articlesAction() {
    $sql = "SELECT idItem, title, body, date, hitNum, replyNum, stereotype, locked, contributorName, correlationCode, idMember FROM Item WHERE (stereotype = ".ARTICLE.") ORDER BY date DESC";
    $sql .= LIMIT;

    $result = mysql_query($sql, $connection) or die(mysql_error());

    while ($item = mysql_fetch_object($result)) {
      $item->title = utf8_encode($item->title);
      $item->body = utf8_encode($item->body);
      $item->contributorName = utf8_encode($item->contributorName);
      $item->correlationCode = utf8_encode($item->contributorName);

      $doc = new Article;
      //$doc->docClass = '\PitPress\Model\Article';
      $doc->assignObject($item);

      $couch->saveDoc($doc);
    }

    mysql_free_result($result);
  }


  //! @brief Imports books.
  public function booksAction() {
    $sql = "SELECT idItem, title, body, date, hitNum, replyNum, stereotype, locked, contributorName, idMember FROM Item WHERE (stereotype = ".BOOK.") ORDER BY date DESC";
    $sql .= LIMIT;

    $result = mysql_query($sql, $connection) or die(mysql_error());

    while ($item = mysql_fetch_object($result)) {
      $item->title = utf8_encode($item->title);

      $body = $item->body;
      unset($item->body);

      preg_match('/\\[isbn\\](.*?)\\[\/isbn\\]/s', $body, $matches);
      $item->isbn = utf8_encode($matches[1]);
      preg_match('/\\[authors\\](.*?)\\[\/authors\\]/s', $body, $matches);
      $item->authors = utf8_encode($matches[1]);
      preg_match('/\\[publisher\\](.*?)\\[\/publisher\\]/s', $body, $matches);
      $item->publisher = utf8_encode($matches[1]);
      preg_match('/\\[language\\](.*?)\\[\/language\\]/s', $body, $matches);
      $item->language = utf8_encode($matches[1]);
      preg_match('/\\[year\\](.*?)\\[\/year\\]/s', $body, $matches);
      $item->year = $matches[1];
      preg_match('/\\[pages\\](.*?)\\[\/pages\\]/s', $body, $matches);
      $item->pages = $matches[1];
      preg_match('/\\[attachments\\](.*?)\\[\/attachments\\]/s', $body, $matches);
      $item->attachments = utf8_encode($matches[1]);
      preg_match('/\\[review\\](.*?)\\[\/review\\]/s', $body, $matches);
      $item->review = utf8_encode($matches[1]);
      preg_match('/\\[positive\\](.*?)\\[\/positive\\]/s', $body, $matches);
      $item->positive = utf8_encode($matches[1]);
      preg_match('/\\[negative\\](.*?)\\[\/negative\\]/s', $body, $matches);
      $item->negative = utf8_encode($matches[1]);
      preg_match('/\\[vendorLink\\](.*?)\\[\/vendorLink\\]/s', $body, $matches);
      $item->vendorLink = utf8_encode($matches[1]);

      $item->contributorName = utf8_encode($item->contributorName);

      $doc = new Book;
      //$doc->docClass = '\PitPress\Model\Book';
      $doc->assignObject($item);

      $couch->saveDoc($doc);
    }

    mysql_free_result($result);
  }


  //! @brief Imports tags.
  public function tagsAction() {
    $sql = "SELECT idCategory, name, lastUpdate, passed FROM Category";

    $result = mysql_query($sql, $connection) or die(mysql_error());

    while ($item = mysql_fetch_object($result)) {
      $item->name = utf8_encode($item->name);

      $doc = new Tag;
      //$doc->docClass = '\PitPress\Model\Category';
      $doc->assignObject($item);

      $couch->saveDoc($doc);
    }

    mysql_free_result($result);
  }

}
<?php

//! @file import.php
//! @brief Imports data from Programmazione.it v6.4.
//! @details
//! @author Filippo F. Fadda

error_reporting (E_ALL ^ E_NOTICE);

$start = microtime(true);

require __DIR__ . "/../vendor/autoload.php";

use ElephantOnCouch\ElephantOnCouch;
use PitPress\Model\Article;
use PitPress\Model\Book;

const COMPUTER_NAME = "localhost";
const DATABASE_NAME = "programmazione";
const LOGIN = "root";
const PASSWORD = "cathedral";

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

const COUCH_USER = "pippo";
const COUCH_PASSWORD = "calippo";
const COUCH_DATABASE = "test_blog";

const USE_CURL = TRUE;
const FIRST_RUN = FALSE;


$connection = mysql_connect(COMPUTER_NAME, LOGIN, PASSWORD) or die(mysql_error());
mysql_select_db(DATABASE_NAME) or die(mysql_error());

try {
  $couch = new ElephantOnCouch(ElephantOnCouch::DEFAULT_SERVER, COUCH_USER, COUCH_PASSWORD);

  if (USE_CURL)
    $couch->useCurl();

  if (!FIRST_RUN)
    $couch->deleteDb(COUCH_DATABASE);

  $couch->createDb(COUCH_DATABASE);


  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // IMPORT ARTICLES

  $sql = "SELECT idItem, title, body, date, hitNum, replyNum, stereotype, locked, contributorName, correlationCode, idMember FROM Item WHERE (stereotype = ".ARTICLE.") ORDER BY date DESC";
  $sql .= LIMIT;

  $result = mysql_query($sql, $connection) or die(mysql_error());

  while ($item = mysql_fetch_object($result)) {
    $item->title = utf8_encode($item->title);
    $item->body = utf8_encode($item->body);
    $item->contributorName = utf8_encode($item->contributorName);

    $doc = new Article;
    //$doc->docClass = '\PitPress\Model\Article';
    $doc->assignObject($item);

    $couch->saveDoc($doc);
  }

  mysql_free_result($result);


  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // IMPORT BOOKS

  $sql = "SELECT idItem, title, body, date, hitNum, replyNum, stereotype, locked, contributorName, correlationCode, idMember FROM Item WHERE (stereotype = ".BOOK.") ORDER BY date DESC";
  $sql .= LIMIT;

  $result = mysql_query($sql, $connection) or die(mysql_error());

  while ($item = mysql_fetch_object($result)) {
    $item->title = utf8_encode($item->title);
    $item->body = utf8_encode($item->body);
    $item->contributorName = utf8_encode($item->contributorName);

    $doc = new Book;
    //$doc->docClass = '\PitPress\Model\Book';
    $doc->assignObject($item);

    $couch->saveDoc($doc);
  }

  mysql_free_result($result);


  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // IMPORT TAGS

  $sql = "SELECT idCategory, name, lastUpdate, passed FROM Category";

  $result = mysql_query($sql, $connection) or die(mysql_error());

  while ($item = mysql_fetch_object($result)) {
    $item->name = utf8_encode($item->name);

    $doc = new Category;
    //$doc->docClass = '\PitPress\Model\Category';
    $doc->assignObject($item);

    $couch->saveDoc($doc);
  }

  mysql_free_result($result);


  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // IMPORT USERS

  $sql = "SELECT idCategory, name, lastUpdate, passed FROM Category";

  $result = mysql_query($sql, $connection) or die(mysql_error());

  while ($item = mysql_fetch_object($result)) {
    $item->name = utf8_encode($item->name);

    $doc = new Category;
    //$doc->docClass = '\PitPress\Model\Category';
    $doc->assignObject($item);

    $couch->saveDoc($doc);
  }

  mysql_free_result($result);

}
catch (Exception $e) {
  echo $e;
}

$stop = microtime(true);
$time = round($stop - $start, 3);

echo "\r\n\r\n\r\nElapsed time: $time";
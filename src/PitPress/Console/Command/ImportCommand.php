<?php

//! @file ImportCommand.php
//! @brief This file contains the ImportCommand class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Console\Command;


use PitPress\Model\Accessory\Classification;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use PitPress\Model\Article;
use PitPress\Model\Book;
use PitPress\Model\Tag;
use PitPress\Model\User;


//! @brief Imports into CouchDB the data from Programmazione.it v6.4 MySQL database.
//! @nosubgrouping
class ImportCommand extends AbstractCommand {

  const ARTICLE_DRAFT = 0;
  const ARTICLE = 2;

  const INFORMATIVE = 1;
  const ERROR = 3;
  const DOWNLOAD = 133;

  const BOOK_DRAFT = 10;
  const BOOK = 11;

  const DISCUSSION_DRAFT = 30;
  const DISCUSSION = 31;

  private $limit;

  protected $mysql;
  protected $couch;


  //! @brief Imports users.
  private function importUsers() {
    //$sql = "SELECT idMember, name AS firstName, surname AS lastName, nickName AS displayName, email, password, sex, birthDate AS birthday, ipAddress, confirmHash AS confirmationHash, confirmed AS authenticated, regDate AS creationDate, lastUpdate, avatarData, avatarType, realNamePcy FROM Member";
    $sql = "SELECT idMember, name AS firstName, surname AS lastName, nickName AS displayName, email, password, sex, birthDate AS birthday, ipAddress, confirmHash AS confirmationHash, confirmed AS authenticated, regDate AS creationDate, lastUpdate, realNamePcy FROM Member";
    $sql .= $this->limit;

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    while ($item = mysqli_fetch_object($result)) {
      $item->firstName = utf8_encode($item->firstName);
      $item->lastName = utf8_encode($item->lastName);
      $item->displayName = utf8_encode($item->displayName);
      $item->email = utf8_encode($item->email);
      $item->password = utf8_encode($item->password);
      $item->confirmationHash = utf8_encode($item->confirmationHash);

      $doc = new User;
      //$doc->docClass = '\PitPress\Model\Category';
      $doc->assignObject($item);

      $this->couch->saveDoc($doc);
    }

    mysqli_free_result($result);
  }


  //! @brief Imports articles.
  public function importArticles() {
    $sql = "SELECT idItem, title, body, date, hitNum, replyNum, stereotype, locked, contributorName, correlationCode, idMember FROM Item WHERE (stereotype = ".self::ARTICLE.") ORDER BY date DESC";
    $sql .= $this->limit;

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    while ($item = mysqli_fetch_object($result)) {
      $item->title = utf8_encode($item->title);
      $item->body = utf8_encode($item->body);
      $item->contributorName = utf8_encode($item->contributorName);
      $item->correlationCode = utf8_encode($item->contributorName);

      $doc = new Article;
      //$doc->docClass = '\PitPress\Model\Article';
      $doc->assignObject($item);

      $this->couch->saveDoc($doc);
    }

    mysqli_free_result($result);
  }


  //! @brief Imports books.
  public function importBooks() {
    $sql = "SELECT idItem, title, body, date, hitNum, replyNum, stereotype, locked, contributorName, idMember FROM Item WHERE (stereotype = ".self::BOOK.") ORDER BY date DESC";
    $sql .= $this->limit;

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    while ($item = mysqli_fetch_object($result)) {
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

      if (preg_match('/\\[vendorLink\\](.*?)\\[\/vendorLink\\]/s', $body, $matches) === 1);
        $item->vendorLink = utf8_encode($matches[1]);

      $item->contributorName = utf8_encode($item->contributorName);

      $doc = new Book;
      $doc->assignObject($item);

      $this->couch->saveDoc($doc);
    }

    mysqli_free_result($result);
  }


  //! @brief Imports tags.
  public function importTags() {
    $sql = "SELECT idCategory, name, lastUpdate, passed FROM Category";
    $sql .= $this->limit;

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    while ($item = mysqli_fetch_object($result)) {
      $item->name = utf8_encode(strtolower(strstr($item->name, " ", "-")));

      $doc = new Tag;
      $doc->assignObject($item);

      $this->couch->saveDoc($doc);
    }

    mysqli_free_result($result);
  }


  //! @brief Imports classifications.
  public function importClassifications() {
    $sql = "SELECT I.id AS itemId, C.id AS tagId FROM Item I, Category C, ItemsXCategory X WHERE I.idItem = X.idItem AND C.idCategory = X.idCategory";
    $sql .= $this->limit;

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    while ($item = mysqli_fetch_object($result)) {
      $doc = new Classification($item->itemId, $item->tagId);

      $this->couch->saveDoc($doc);
    }

    mysqli_free_result($result);
  }


  //! @brief Import all entities.
  private function importAll() {
    $this->importUsers();
    $this->importArticles();
    $this->importBooks();
    $this->importTags();
    $this->importClassifications();
  }


  //! @brief Configures the command.
  protected function configure() {
    $this->setName("import");
    $this->setDescription("Imports into CouchDB the data from Programmazione.it v6.4 MySQL database.");
    $this->addArgument("entities",
        InputArgument::IS_ARRAY | InputArgument::REQUIRED,
        "The entities you want import. Use 'all' if you want import all the entities, 'users' if you want just import the
        users or separate multiple entities with a space. The available entities are: users, articles, books, tags");
    $this->addOption("limit",
        NULL,
        InputOption::VALUE_OPTIONAL,
        "Limit the imported articles. This option is applied only when 'articles' argument is provided, alone or as a
        part of the array, else is ignored");
  }


  //! @brief Executes the command.
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->mysql = $this->_di['mysql'];
    $this->couch = $this->_di['couchdb'];

    $entities = $input->getArgument('entities');
    $limit = (int)$input->getOption('limit');

    if ($limit > 0)
      $this->limit = " LIMIT ".(string)$limit;
    else
      $this->limit = "";

    // Checks if the argument 'all' is provided.
    $index = array_search("all", $entities);

    if ($index === FALSE) {

      foreach ($entities as $name)
        switch ($name) {
          case 'users':
            $this->importUsers();
            break;

          case 'articles':
            $this->importArticles();
            break;

          case 'books':
            $this->importBooks();
            break;

          case 'tags':
            $this->importTags();
            break;

          case 'classifications':
            $this->importClassifications();
            break;
        }

    }
    else
      $this->importAll();
  }

}
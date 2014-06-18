<?php

/**
 * @file ImportCommand.php
 * @brief This file contains the ImportCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use PitPress\Model\Blog\Article;
use PitPress\Model\Blog\Book;
use PitPress\Model\Tag\Tag;
use PitPress\Model\User\User;
use PitPress\Model\Reply;
use PitPress\Model\Accessory\Star;
use PitPress\Model\Accessory\Classification;
use PitPress\Model\Accessory\Subscription;
use PitPress\Helper\Text;

use Converter\BBCodeConverter;
use Converter\HTMLConverter;

use ElephantOnCouch\Generator\UUID;


/**
 * @brief Imports into CouchDB the data from Programmazione.it v6.4 MySQL database.
 * @nosubgrouping
 * @todo: Download and save images as article attachments.
 * @todo: Save attachments.
 * @todo: Convert [center][/center] to Markdown.
 * @todo: Convert quotes.
 */
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

  private $mysql;
  private $couch;
  private $redis;
  private $markdown;

  private $input;
  private $output;


  /**
   * @brief Removes from the title the page number.
   */
  private function purgeTitle($title) {
    return Text::convertCharset(rtrim(preg_replace('%\(\d*/\d*\)%iu', '', stripslashes($title)), '\t\n\r\0\x0B'));
  }


  /**
   * @brief Get everything after the `: ` sequence of characters.
   */
  private function getSubtitle($title) {
    if (preg_match('/: (.*)/iu', $title, $matches))
      return rtrim($matches[1]);
    else
      return "";
  }


  /**
   * @brief Converts the text to utf8, then bbcode and finally markdown.
   * @param[in] string $text The text to convert.
   * @param[in] integer $id An identifier to be used in case an error occurs.
   * @return string The converted text.
   */
  private function convertText($text, $id) {
    $utf8 = Text::convertCharset($text);
    $bbcode = Text::htmlToBBCode($utf8, $id);
    return Text::bbcodeToMarkdown($bbcode, $id);
  }


  private function importRelated($postId) {
    $this->importClassifications($postId);
    $this->importReplies($postId);
    $this->importFavorites($postId);
    $this->importSubscriptions($postId);
  }


  private function processArticle($item) {
    $article = new Article();

    $article->id = $item->id;
    $article->publishingDate = (int)$item->unixTime;
    $article->title = Text::convertCharset($item->title, TRUE);

    if (isset($item->userId))
      $article->userId = $item->userId;

    $article->body = $this->convertText($item->body, $item->idItem);

    try {
      $article->html = $this->markdown->parse($article->body);
    }
    catch(\Exception $e) {
      $this->monolog->addCritical(sprintf(" %d - %s", $item->idItem, $article->title));
    }

    $purged = Text::purge($article->html);
    $article->excerpt = Text::truncate($purged);

    // We finally save the article.
    try {
      //$this->couch->saveDoc($article);
      $article->save();
    }
    catch(\Exception $e) {
      $this->monolog->addCritical($e);
      $this->monolog->addCritical(sprintf("Invalid JSON: %d - %s", $item->idItem, $article->title));
    }

    // We update the article views.
    $this->redis->hSet($article->id, 'hits', $item->hitNum);

    // We update the article downloads.
    if ($item->downloadNum > 0)
      $this->redis->hSet($article->id, 'downloads', $item->downloadNum);

    $this->importRelated($article->id);
  }


  private function processBook($item) {
    $book = new Book();

    $book->id = $item->id;
    $book->publishingDate = (int)$item->unixTime;
    $book->title = Text::convertCharset($item->title, TRUE);

    if (isset($item->userId))
      $book->userId = $item->userId;

    $body = stripslashes($item->body);

    if (preg_match('/\\[isbn\\](.*?)\\[\/isbn\\]/s', $body, $matches))
      $book->isbn = Text::convertCharset($matches[1]);
    if (preg_match('/\\[authors\\](.*?)\\[\/authors\\]/s', $body, $matches))
      $book->authors = Text::convertCharset($matches[1]);
    if (preg_match('/\\[publisher\\](.*?)\\[\/publisher\\]/s', $body, $matches))
      $book->publisher = Text::convertCharset($matches[1]);
    if (preg_match('/\\[language\\](.*?)\\[\/language\\]/s', $body, $matches))
      $book->language = Text::convertCharset($matches[1]);
    if (preg_match('/\\[year\\](.*?)\\[\/year\\]/s', $body, $matches))
      $book->year = $matches[1];
    if (preg_match('/\\[pages\\](.*?)\\[\/pages\\]/s', $body, $matches))
      $book->pages = $matches[1];
    if (preg_match('/\\[attachments\\](.*?)\\[\/attachments\\]/s', $body, $matches) && !empty($matches[1]))
      $book->attachments = Text::convertCharset($matches[1]);
    if (preg_match('/\\[review\\](.*?)\\[\/review\\]/s', $body, $matches))
      $review = Text::convertCharset($matches[1]);
    if (preg_match('/\\[positive\\](.*?)\\[\/positive\\]/s', $body, $matches))
      $positive = Text::convertCharset($matches[1]);
    if (preg_match('/\\[negative\\](.*?)\\[\/negative\\]/s', $body, $matches))
      $negative = Text::convertCharset($matches[1]);

    if (preg_match('/\\[vendorLink\\](.*?)\\[\/vendorLink\\]/s', $body, $matches) && !empty($matches[1]))
      $book->link = Text::convertCharset($matches[1]);

    $book->body = Text::bbcodeToMarkdown($review, $item->id);

    try {
      $book->html = $this->markdown->parse($book->body);
    }
    catch(\Exception $e) {
      $this->monolog->addCritical(sprintf(" %d - %s", $item->idItem, $book->title));
    }

    $purged = Text::purge($book->html);
    $book->excerpt = Text::truncate($purged);

    $book->positive = Text::bbcodeToMarkdown($positive, $item->id);
    $book->negative = Text::bbcodeToMarkdown($negative, $item->id);

    // We finally save the book.
    try {
      //$this->couch->saveDoc($book);
      $book->save();
    }
    catch(\Exception $e) {
      $this->monolog->addCritical(sprintf("Invalid JSON: %d - %s", $item->idItem, $book->title));
    }

    // We update the book views.
    $this->redis->hSet($book->id, 'hits', $item->hitNum);

    $this->importRelated($book->id);
  }


  /**
   * @brief Imports tags.
   */
  protected function importTags() {
    $this->output->writeln("Importing tags...");

    $sql = "SELECT id FROM Member WHERE idMember = 1";
    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));
    $userId = mysqli_fetch_array($result)['id'];
    mysqli_free_result($result);

    $sql = "SELECT id, idCategory, name, UNIX_TIMESTAMP(lastUpdate) AS unixTime, passed FROM Category";
    $sql .= $this->limit;

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    $rows = mysqli_num_rows($result);
    $progress = $this->getApplication()->getHelperSet()->get('progress');
    $progress->start($this->output, $rows);

    while ($item = mysqli_fetch_object($result)) {
      $tag = new Tag();

      $tag->id = $item->id;
      $tag->publishingDate = (int)$item->unixTime;
      $tag->name = Text::convertCharset(strtolower(str_replace(" ", "-", stripslashes($item->name))));
      $tag->userId = $userId;

      $this->couch->saveDoc($tag);

      $progress->advance();
    }

    mysqli_free_result($result);

    $progress->finish();
  }


  /**
   * @brief Imports classifications.
   */
  protected function importClassifications($postId) {
    $sql = "SELECT I.id AS postId, C.id AS tagId, I.stereotype AS stereotype, UNIX_TIMESTAMP(I.date) AS unixTime FROM Item I, Category C, ItemsXCategory X WHERE I.idItem = X.idItem AND C.idCategory = X.idCategory AND I.id = '".$postId."'";

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    while ($item = mysqli_fetch_object($result)) {

      if ($item->stereotype == self::ARTICLE)
        $postType = 'article';
      else
        $postType = 'book';

      $doc = Classification::create($item->postId, $postType, 'blog', $item->tagId, (int)$item->unixTime);

      $this->couch->saveDoc($doc);
    }

    mysqli_free_result($result);
  }


  /**
   * @brief Imports favorites.
   */
  protected function importFavorites($postId) {
    $sql = "SELECT I.id AS itemId, I.stereotype as stereotype, M.id AS userId, UNIX_TIMESTAMP(F.date) AS timestamp FROM Item I, Member M, Favourite F WHERE I.idItem = F.idItem AND M.idMember = F.idMember AND I.id = '".$postId."'";

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    while ($item = mysqli_fetch_object($result)) {
      $timestamp = (int)$item->timestamp;

      if ($item->stereotype == self::ARTICLE)
        $itemType = 'article';
      else
        $itemType = 'book';

      if ($timestamp > 0)
        $doc = Star::create($item->userId, $item->itemId, $itemType, $timestamp);
      else
        $doc = Star::create($item->userId, $item->itemId, $itemType);

      $this->couch->saveDoc($doc);
    }

    mysqli_free_result($result);
  }


  /**
   * @brief Imports subscriptions.
   */
  protected function importSubscriptions($postId) {
    $sql = "SELECT I.id AS itemId, M.id AS userId, UNIX_TIMESTAMP(T.creationTime) AS timestamp FROM Item I, Member M, Thread T WHERE I.idItem = T.idItem AND M.idMember = T.idMember AND I.id = '".$postId."'";

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    while ($item = mysqli_fetch_object($result)) {
      $timestamp = (int)$item->timestamp;

      if ($timestamp > 0)
        $doc = Subscription::create($item->itemId, $item->userId, $timestamp);
      else
        $doc = Subscription::create($item->itemId, $item->userId);

      $this->couch->saveDoc($doc);
    }

    mysqli_free_result($result);
  }


  /**
   * @brief Imports comments.
   */
  protected function importReplies($postId) {
    $sql = "SELECT C.idComment, I.id AS postId, M.id AS userId, UNIX_TIMESTAMP(C.date) AS unixTime, C.body FROM Comment C, Item I, Member M WHERE C.idItem = I.idItem AND C.idMember = M.idMember AND I.id = '".$postId."' ORDER BY C.date DESC, idComment DESC";

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    while ($item = mysqli_fetch_object($result)) {
      try {
        $replay = new Reply();

        $replay->id = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING);
        $replay->publishingDate = (int)$item->unixTime;
        $replay->postId = $item->postId;
        $replay->userId = $item->userId;

        $utf8 = Text::convertCharset($item->body, TRUE);
        $bbcode = Text::htmlToBBCode($utf8, $item->idComment);
        $replay->body = Text::bbcodeToMarkdown($bbcode, $item->idComment);

        $replay->html = $this->markdown->parse($replay->body);
      }
      catch(\Exception $e) {
        $this->monolog->addCritical(sprintf(" Commento %d - Item %d", $item->idComment, $item->postId));
      }

      // We finally save the comment.
      try {
          //$this->couch->saveDoc($article);
          $replay->save();
      }
      catch(\Exception $e) {
        $this->monolog->addCritical($e);
        $this->monolog->addCritical(sprintf("Invalid JSON: %d", $item->idComment));
      }
    }

    mysqli_free_result($result);
  }


  private function mergeArticles($correlationCode) {
    $article = new Article();

    // To avoid a stupid notice.
    $body = "";

    $sql = "SELECT idItem, I.id AS id, M.id AS userId, contributorName, I.title, I.body, UNIX_TIMESTAMP(date) AS unixTime, hitNum, downloadNum, locked FROM Item I LEFT OUTER JOIN Member M USING (idMember) WHERE correlationCode = '".$correlationCode."' ORDER BY date ASC, idItem ASC";

    $pages = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    $paragraphTitle = "";
    $importedArticles = [];
    while ($page = mysqli_fetch_object($pages)) {
      $pageBody = $this->convertText($page->body, $page->idItem);
      $title = $this->purgeTitle($page->title);
      $subtitle = $this->getSubtitle($title);

      /*
      $encodings = [
        'Windows-1252',
        'LATIN1',
        'UTF-8'
      ];
      $encoding = mb_detect_encoding($page->body, $encodings);
      $this->monolog->addNotice(sprintf("Encodig: %s", $encoding));
      */

      /*
      if ($page->id == 'fae33062-6cbf-448f-a601-11a549428f4a') {
        $this->monolog->addNotice("PRIMA DELLA TRASFORMAZIONE");
        $this->monolog->addNotice(sprintf("%s", $page->body));
        $this->monolog->addNotice("DOPO LA TRASFORMAZIONE");
        $this->monolog->addNotice(sprintf("%s", $pageBody));
      }
      */

      if (empty($body)) {
        $article->id = $page->id;
        $article->publishingDate = (int)$page->unixTime;

        if (isset($page->userId))
          $article->userId = $page->userId;

        if (!empty($subtitle))
          $article->title = rtrim(strstr($title, $subtitle, TRUE), ": \t\n\r\0\x0B");
        else
          $article->title = $title;

        $this->redis->hSet($article->id, 'hits', $page->hitNum);

        $this->importRelated($page->id);
      }
      else {
        $body .= PHP_EOL.PHP_EOL;

        $this->redis->hIncrBy($article->id, 'hits', $page->hitNum);
      }

      if (!empty($subtitle) && $subtitle != $paragraphTitle) {
        $body .= Text::capitalize($subtitle).PHP_EOL;
        $body .= str_repeat("-", mb_strlen($subtitle)).PHP_EOL.PHP_EOL;
      }

      $body .= $pageBody;

      $paragraphTitle = $subtitle;
      $importedArticles[$page->id] = NULL;
    }

    $article->body = $body;

    try {
      $article->html = $this->markdown->parse($article->body);
    }
    catch(\Exception $e) {
      $this->monolog->addCritical(sprintf(" %d - %s", $page->idItem, $article->title));
    }

    // We finally save the article.
    try {
      $purged = Text::purge($article->html);
      $article->excerpt = Text::truncate($purged);

      //$this->couch->saveDoc($article);
      $article->save();
    }
    catch(\Exception $e) {
      $this->monolog->addCritical($e);
      $this->monolog->addCritical(sprintf("Invalid JSON: %d - %s", $page->idItem, $article->title));
    }

    return $importedArticles;
  }


  /**
   * @brief Imports multi-page articles.
   */
  protected function importMultiPageArticles() {
    $this->output->writeln("Importing multi-page articles...");

    $sql = "SELECT correlationCode FROM Item WHERE (stereotype = ".self::ARTICLE.") GROUP BY correlationCode HAVING COUNT(correlationCode) > 1 ORDER BY date ASC, idItem ASC";
    $sql .= $this->limit;

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    $rows = mysqli_num_rows($result);
    $progress = $this->getApplication()->getHelperSet()->get('progress');
    $progress->start($this->output, $rows);

    $imported = [];
    while ($correlationCode = mysqli_fetch_assoc($result)['correlationCode']) {
      $imported = $imported + $this->mergeArticles($correlationCode);

      $progress->advance();
    }

    mysqli_free_result($result);

    $progress->finish();

    return $imported;
  }


  /**
   * @brief Imports items (articles and books).
   */
  protected function importItems() {
    $importedItems = $this->importMultiPageArticles();

    $this->output->writeln("Importing single-page articles and books...");

    $sql = "SELECT I.stereotype, idItem, I.id AS id, M.id AS userId, contributorName, I.title, body, UNIX_TIMESTAMP(date) AS unixTime, hitNum, downloadNum, locked FROM Item I LEFT OUTER JOIN Member M USING (idMember) WHERE (stereotype = 2) OR (stereotype = 11) ORDER BY unixTime ASC, idItem ASC";
    $sql .= $this->limit;

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    $rows = mysqli_num_rows($result);
    $progress = $this->getApplication()->getHelperSet()->get('progress');
    $progress->start($this->output, $rows);

    while ($item = mysqli_fetch_object($result)) {
      // Checks if the article has already been imported.
      if (array_key_exists($item->id, $importedItems)) {
        $progress->advance();
        continue;
      }

      if ($item->stereotype == self::ARTICLE)
        $this->processArticle($item);
      else
        $this->processBook($item);

      $progress->advance();
    }

    mysqli_free_result($result);

    $progress->finish();
  }


  /**
   * @brief Imports users.
   */
  protected function importUsers() {
    $this->output->writeln("Importing users...");

    //$sql = "SELECT idMember, name AS firstName, surname AS lastName, nickName AS displayName, email, password, sex, birthDate AS birthday, ipAddress, confirmHash AS confirmationHash, confirmed AS authenticated, regDate AS creationDate, lastUpdate, avatarData, avatarType, realNamePcy FROM Member";
    $sql = "SELECT id, name AS firstName, surname AS lastName, nickName AS displayName, email, password, sex, UNIX_TIMESTAMP(birthDate) AS birthday, ipAddress, confirmHash AS confirmationHash, confirmed, UNIX_TIMESTAMP(regDate) AS creationDate, lastUpdate, realNamePcy FROM Member";
    $sql .= $this->limit;

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    $rows = mysqli_num_rows($result);
    $progress = $this->getApplication()->getHelperSet()->get('progress');
    $progress->start($this->output, $rows);

    while ($item = mysqli_fetch_object($result)) {
      $user = new User();

      $user->id = $item->id;
      $user->firstName = Text::convertCharset($item->firstName, TRUE);
      $user->lastName = Text::convertCharset($item->lastName, TRUE);
      $user->displayName = Text::convertCharset($item->displayName, TRUE);
      $user->email = Text::convertCharset($item->email);
      $user->password = Text::convertCharset($item->password);
      $user->birthday = (int)$item->birthday;
      $user->sex = $item->sex;
      $user->internetProtocolAddress = Text::convertCharset($item->ipAddress);
      $user->creationDate = (int)$item->creationDate;
      $user->confirmationHash = Text::convertCharset($item->confirmationHash);

      if ($item->confirmed == 1)
        $user->confirm();

      $this->couch->saveDoc($user);

      $progress->advance();
    }

    mysqli_free_result($result);

    $progress->finish();
  }


  /**
   * @brief Imports all entities.
   */
  private function importAll() {
    $this->importUsers();
    $this->importTags();
    $this->importItems();
  }


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("import");
    $this->setDescription("Imports into CouchDB the data from Programmazione.it v6.4 MySQL database.");
    $this->addArgument("entities",
        InputArgument::IS_ARRAY | InputArgument::REQUIRED,
        "The entities you want import. Use 'all' if you want import all the entities, 'users' if you want just import the
        users or separate multiple entities with a space. The available entities are: users, tags, items.");
    $this->addOption("limit",
        NULL,
        InputOption::VALUE_OPTIONAL,
        "Limit the imported records.");
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->mysql = $this->di['mysql'];
    $this->couch = $this->di['couchdb'];
    $this->redis = $this->di['redis'];
    $this->markdown = $this->di['markdown'];

    $this->input = $input;
    $this->output = $output;

    $entities = $input->getArgument('entities');
    $limit = (int)$input->getOption('limit');

    // Checks if the argument 'all' is provided.
    $index = array_search("all", $entities);

    if ($limit > 0 && $index === FALSE)
      $this->limit = " LIMIT ".(string)$limit;
    else
      $this->limit = "";

    if ($index === FALSE) {

      foreach ($entities as $name)
        switch ($name) {
          case 'users':
            $this->importUsers();
            break;

          case 'tags':
            $this->importTags();
            break;

          case 'items':
            $this->importItems();
            break;
        }

    }
    else
      $this->importAll();

    $this->couch->ensureFullCommit();
  }

}
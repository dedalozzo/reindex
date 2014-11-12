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

use PitPress\Model\Post;
use PitPress\Model\Article;
use PitPress\Model\Book;
use PitPress\Model\Tag;
use PitPress\Model\User;
use PitPress\Model\Reply;
use PitPress\Model\Accessory\Star;
use PitPress\Model\Accessory\Subscription;
use PitPress\Helper\Text;

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
    $temp = Text::convertCharset(rtrim($title, '\t\n\r\0\x0B'), TRUE);
    return preg_replace('%\(\d*/\d*\)%iu', '', $temp);
  }


  /**
   * @brief Removes the subtitle from the title.
   */
  private function pruneTitle($title, $subtitle = "") {
    if (!empty($subtitle))
      return rtrim(mb_strstr($title, $subtitle, TRUE, "UTF-8"), ": \t\n\r\0\x0B");
    else
      return $title;
  }


  /**
   * @brief Gets everything after the `,` or `: ` sequence of characters.
   */
  private function extractSubtitle($title) {
    if (preg_match('/(?::|,) (.*)/miu', $title, $matches))
      return rtrim($matches[1]);
    else
      return "";
  }


  /**
   * @brief Left trim and capitalize the subtitle.
   */
  private function formatSubtitle($subtitle) {
    return Text::capitalize(ltrim($subtitle));
  }


  /**
   * @brief Converts the text to utf8, then bbcode and finally markdown.
   * @param[in] string $text The text to convert.
   * @param[in] integer $id An identifier to be used in case an error occurs.
   * @return string The converted text.
   */
  private function convertText($text, $id) {
    $utf8 = Text::convertCharset($text, TRUE);
    $bbcode = Text::htmlToBBCode($utf8, $id);
    return Text::bbcodeToMarkdown($bbcode, $id);
  }


  private function importRelated(Post $post) {
    $this->importReplies($post);
    $this->importFavorites($post);
    $this->importSubscriptions($post);
  }


  private function processArticle($item) {
    $article = Article::create($item->id);

    $article->type = 'article';
    $article->userId = $item->userId;
    $article->createdAt = (int)$item->unixTime;
    $article->publishedAt = $article->createdAt;
    $article->title = Text::convertCharset($item->title, TRUE);
    $article->body = $this->convertText($item->body, $item->idItem);

    $this->importClassifications($article);

    // We finally save the article.
    try {
      $article->approve();
      $article->save(TRUE);
    }
    catch(\Exception $e) {
      $this->monolog->addCritical(sprintf("Invalid JSON: %s - %s", $article->id, $article->title));
    }

    // We update the article views.
    $this->redis->hSet($article->unversionId, 'hits', $item->hitNum);

    // We update the article downloads.
    if ($item->downloadNum > 0)
      $this->redis->hSet($article->id, 'downloads', $item->downloadNum);

    $this->importRelated($article);
  }


  private function processBook($item) {
    $book = Book::create($item->id);

    $book->type = 'book';
    $book->userId = $item->userId;
    $book->createdAt = (int)$item->unixTime;
    $book->publishedAt = $book->createdAt;

    $book->title = Text::convertCharset($item->title, TRUE);

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
    $book->positive = Text::bbcodeToMarkdown($positive, $item->id);
    $book->negative = Text::bbcodeToMarkdown($negative, $item->id);

    $this->importClassifications($book);

    // We finally save the book.
    try {
      $book->approve();
      $book->save(TRUE);
    }
    catch(\Exception $e) {
      $this->monolog->addCritical(sprintf("Invalid JSON: %s - %s", $book->id, $book->title));
    }

    // We update the book views.
    $this->redis->hSet($book->unversionId, 'hits', $item->hitNum);

    $this->importRelated($book);
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
      $tag = Tag::create($item->id);

      $tag->createdAt = (int)$item->unixTime;
      $tag->name = Text::convertCharset(strtolower(str_replace(" ", "-", stripslashes($item->name))));
      $tag->userId = $userId;

      $tag->approve();

      $this->couch->saveDoc($tag);

      $progress->advance();
    }

    mysqli_free_result($result);

    $progress->finish();
  }


  /**
   * @brief Imports classifications.
   */
  protected function importClassifications(Post $post) {
    $sql = "SELECT C.id AS tagId, UNIX_TIMESTAMP(I.date) AS unixTime FROM Item I, Category C, ItemsXCategory X WHERE I.idItem = X.idItem AND C.idCategory = X.idCategory AND I.id = '".$post->unversionId."'";

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    while ($item = mysqli_fetch_object($result)) {
      //$doc = Classification::create($post, $item->tagId, (int)$item->unixTime);
      $post->addTagId($item->tagId);
    }

    mysqli_free_result($result);
  }


  /**
   * @brief Imports favorites.
   */
  protected function importFavorites(Post $post) {
    $sql = "SELECT I.id AS itemId, I.stereotype as stereotype, M.id AS userId, UNIX_TIMESTAMP(I.date) AS publishedAt, UNIX_TIMESTAMP(F.date) AS addedAt FROM Item I, Member M, Favourite F WHERE I.idItem = F.idItem AND M.idMember = F.idMember AND I.id = '".$post->unversionId."'";

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    while ($item = mysqli_fetch_object($result)) {
      $post->id = $item->itemId;
      $post->userId = $item->userId;
      $post->createdAt = (int)$item->publishedAt;
      $post->publishedAt = $post->createdAt;

      $doc = Star::create($item->userId, $post, (int)$item->addedAt);

      $this->couch->saveDoc($doc);
    }

    mysqli_free_result($result);
  }


  /**
   * @brief Imports subscriptions.
   */
  protected function importSubscriptions(Post $post) {
    $sql = "SELECT M.id AS userId, UNIX_TIMESTAMP(T.creationTime) AS timestamp FROM Item I, Member M, Thread T WHERE I.idItem = T.idItem AND M.idMember = T.idMember AND I.id = '".$post->unversionId."'";

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    while ($item = mysqli_fetch_object($result)) {
      $doc = Subscription::create($post->unversionId, $item->userId, (int)$item->timestamp);
      $this->couch->saveDoc($doc);
    }

    mysqli_free_result($result);
  }


  /**
   * @brief Imports comments.
   */
  protected function importReplies(Post $post) {
    $sql = "SELECT C.idComment, I.idItem, M.id AS userId, UNIX_TIMESTAMP(C.date) AS unixTime, C.body FROM Comment C, Item I, Member M WHERE C.idItem = I.idItem AND C.idMember = M.idMember AND I.id = '".$post->unversionId."' ORDER BY C.date DESC, idComment DESC";

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    while ($item = mysqli_fetch_object($result)) {
      try {
        $replay = new Reply();

        $replay->id = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING);
        $replay->createdAt = (int)$item->unixTime;
        $replay->postId = $post->unversionId;
        $replay->userId = $item->userId;
        $replay->body = $this->convertText($item->body, $item->idComment);
        $replay->html = $this->markdown->parse($replay->body);
      }
      catch(\Exception $e) {
        $this->monolog->addCritical($e);
        $this->monolog->addCritical(sprintf("Invalid Markdown: Comment %s - Item %s", $item->idComment, $item->idItem));
      }

      // We finally save the comment.
      try {
        $replay->save();
      }
      catch(\Exception $e) {
        $this->monolog->addCritical(sprintf("Invalid JSON: Comment %s - Item %s", $item->idComment, $item->idItem));
      }

      $post->zAddLastUpdate($replay->modifiedAt);
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
      $subtitle = $this->extractSubtitle($title);

      /*
      $encodings = [
        'Windows-1252',
        'LATIN1',
        'UTF-8'
      ];
      $encoding = mb_detect_encoding($page->body, $encodings);
      $this->monolog->addNotice(sprintf("Encodig: %s", $encoding));
      */

      // This is the first page, so we set many properties.
      if (empty($body)) {
        $article->id = $page->id;
        $article->type = 'article';
        $article->userId = $page->userId;
        $article->createdAt = (int)$page->unixTime;
        $article->publishedAt = $article->createdAt;
        $article->title = $this->pruneTitle($title, $subtitle);
        //$this->monolog->addNotice(sprintf("%s", $article->title));

        $this->redis->hSet($article->unversionId, 'hits', $page->hitNum);
        $this->importRelated($article);
      }
      else {
        $body .= PHP_EOL.PHP_EOL;
        $this->redis->hIncrBy($article->unversionId, 'hits', $page->hitNum);
      }

      if (!empty($subtitle) && $subtitle != $paragraphTitle) {
        $sub = $this->formatSubtitle($subtitle);
        //$this->monolog->addNotice(sprintf("        %s", $sub));
        $body .= $sub.PHP_EOL;
        $line = str_repeat("-", mb_strlen($sub, "UTF-8"));
        $body .= $line.PHP_EOL.PHP_EOL;
      }

      $body .= $pageBody;

      $paragraphTitle = $subtitle;
      $importedArticles[$page->id] = NULL;
    }

    $article->body = $body;

    // We finally save the article.
    try {
      // We remove temporary the HTML, because it raises a JSON error trying to save it in CouchDB.
      // I don't know why, a double PHP_EOL is required, otherwise any subtitles containing the `à` character will
      // generate a JSON conversion error. Maybe it is a CouchDB bug or maybe it's an Hoedown bug. todo
      //$article->html = "";

      $this->importClassifications($article);

      $article->approve();
      $article->save(TRUE);
    }
    catch(\Exception $e) {
      $this->monolog->addCritical(sprintf("Invalid JSON: %s - %s", $article->id, $article->title));
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

    //$sql = "SELECT idMember, name AS firstName, surname AS lastName, nickName AS username, email, password, sex, birthDate AS birthday, ipAddress, confirmHash AS confirmationHash, confirmed AS authenticated, regDate AS creationDate, lastUpdate, avatarData, avatarType, realNamePcy FROM Member";
    $sql = "SELECT id, name AS firstName, surname AS lastName, nickName AS username, email, password, sex, UNIX_TIMESTAMP(birthDate) AS birthday, ipAddress, confirmHash AS confirmationHash, confirmed, UNIX_TIMESTAMP(regDate) AS createdAt, UNIX_TIMESTAMP(lastUpdate) as modifiedAt, realNamePcy FROM Member";
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
      $user->username = mb_strtolower(preg_replace('/\s+/u', '_', Text::convertCharset($item->username, TRUE)), 'utf-8');
      $user->email = Text::convertCharset($item->email);
      $user->password = Text::convertCharset($item->password);
      $user->birthday = (int)$item->birthday;
      $user->gender = $item->sex; // 0 => undefined, 1 => male, 2 => female.
      $user->internetProtocolAddress = Text::convertCharset($item->ipAddress);
      $user->createdAt = (int)$item->createdAt;
      $user->modifiedAt = is_null($item->modifiedAt) ? $user->createdAt : (int)$item->modifiedAt;
      $user->hash = Text::convertCharset($item->confirmationHash);

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

    parent::execute($input, $output);
  }

}
<?php

//! @file ImportCommand.php
//! @brief This file contains the ImportCommand class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use PitPress\Model\Blog\Article;
use PitPress\Model\Blog\Book;
use PitPress\Model\Tag\Tag;
use PitPress\Model\User\User;
use PitPress\Model\Aggregate;
use PitPress\Model\Accessory\Star;
use PitPress\Model\Accessory\Classification;
use PitPress\Model\Accessory\Subscription;

use ElephantOnCouch\Generator\UUID;


//! @brief Imports into CouchDB the data from Programmazione.it v6.4 MySQL database.
//! @nosubgrouping
//! @todo: Extract excerpt from body and store it.
//! @todo: Download and save images as article attachments.
//! @todo: Convert [center][/center] to Markdown.
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

  private $input;
  private $output;


  //! @brief Imports users.
  private function importUsers() {
    $this->output->writeln("Importing users...");

    //$sql = "SELECT idMember, name AS firstName, surname AS lastName, nickName AS displayName, email, password, sex, birthDate AS birthday, ipAddress, confirmHash AS confirmationHash, confirmed AS authenticated, regDate AS creationDate, lastUpdate, avatarData, avatarType, realNamePcy FROM Member";
    $sql = "SELECT idMember, name AS firstName, surname AS lastName, nickName AS displayName, email, password, sex, UNIX_TIMESTAMP(birthDate) AS birthday, ipAddress, confirmHash AS confirmationHash, confirmed AS authenticated, UNIX_TIMESTAMP(regDate) AS creationDate, lastUpdate, realNamePcy FROM Member";
    $sql .= $this->limit;

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    $rows = mysqli_num_rows($result);
    $progress = $this->getApplication()->getHelperSet()->get('progress');
    $progress->start($this->output, $rows);

    while ($item = mysqli_fetch_object($result)) {
      $user = new User();

      $user->firstName = utf8_encode($item->firstName);
      $user->lastName = utf8_encode($item->lastName);
      $user->displayName = utf8_encode($item->displayName);
      $user->email = utf8_encode($item->email);
      $user->password = utf8_encode($item->password);
      $user->birthday = utf8_encode($item->birthday);
      $user->ipAddress = utf8_encode($item->ipAddress);

      $this->couch->saveDoc($user);

      $progress->advance();
    }

    mysqli_free_result($result);

    $progress->finish();
  }


  //! @brief Imports articles.
  private function importArticles() {
    $this->output->writeln("Importing articles...");

    // select idItem, title from Item where stereotype = 2 and  date < DATE('2005-12-07 12:12') order by date;
    $sql = "SELECT id, idItem, title, body, UNIX_TIMESTAMP(date) AS unixTime, hitNum, replyNum, stereotype, locked, contributorName, correlationCode, idMember FROM Item WHERE (stereotype = ".self::ARTICLE.") ORDER BY date ASC";
    //$sql = "SELECT idItem, title, body, date, hitNum, replyNum, stereotype, locked, contributorName, correlationCode, idMember FROM Item WHERE (stereotype = ".self::ARTICLE.") AND idItem = 30806";

    $sql .= $this->limit;

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    $rows = mysqli_num_rows($result);
    $progress = $this->getApplication()->getHelperSet()->get('progress');
    $progress->start($this->output, $rows);

    while ($item = mysqli_fetch_object($result)) {
      $article = new Article();

      $article->id = $item->id;
      $article->publishingDate = (int)$item->unixTime;
      $article->title = utf8_encode($item->title);
      $article->creator = utf8_encode($item->contributorName);

      $body = $item->body;


      // Let's find all code inside the body. The code can be inside <pre></pre>, <code></code>, or [code][/code] if you
      // are using BBCode markup language.
      $pattern = '%(?P<openpre><pre>)(?P<contentpre>[\W\D\w\s]*?)(?P<closepre></pre>)|(?P<opencode><code>)(?P<contentcode>[\W\D\w\s]*?)(?P<closecode></code>)|(?P<openbbcode>\[code=?\w*\])(?P<contentbbcode>[\W\D\w\s]*?)(?P<closebbcode>\[/code\])%i';

      if (preg_match_all($pattern, $body, $snippets)) {

        $pattern = '%<pre>[\W\D\w\s]*?</pre>|<code>[\W\D\w\s]*?</code>|\[code=?\w*\][\W\D\w\s]*?\[/code\]%i';

        // Replaces the code snippet with a special marker to be able to inject the code in place.
        $body = preg_replace($pattern, '___SNIPPET___', $body);
      }


      // Replace links.
      $body = preg_replace_callback('%(?i)<a[^>]+>(.+?)</a>%',

        function ($matches) use ($item) {

          // Extracts the url.
          if (preg_match('/\s*(?i)href\s*=\s*("([^"]*")|\'[^\']*\'|([^\'">\s]+))/', $matches[0], $others) === 1) {
            $href = strtolower(trim($others[1], '"'));

            // Extracts the target.
            if (preg_match('/\s*(?i)target\s*=\s*("([^"]*")|\'[^\']*\'|([^\'">\s]+))/', $matches[0], $others) === 1)
              $target = strtolower(trim($others[1], '"'));
            else
              $target = "_self";
          }
          else
            throw new \RuntimeException(sprintf("Article with idItem = %d have malformed links", $item->idItem));

          return "[url=".$href." t=".$target."]".$matches[1]."[/url]";

        },

        $body
      );


      // Replace images.
      $body = preg_replace_callback('/<img[^>]+>/i',

        function ($matches) use ($item) {

          // Extracts the src.
          if (preg_match('/\s*(?i)src\s*=\s*("([^"]*")|\'[^\']*\'|([^\'">\s]+))/', $matches[0], $others) === 1)
            $src = strtolower(trim($others[1], '"'));
          else
            throw new \RuntimeException(sprintf("Article with idItem = %d have malformed images", $item->idItem));

          return "[img]".$src."[/img]";

        },

        $body
      );


      // Replace other tags.
      $body = preg_replace_callback('%</?[a-z][a-z0-9]*[^<>]*>%i',

        function ($matches) {
          $tag = strtolower($matches[0]);

          switch ($tag) {
            case ($tag == '<strong>' || $tag == '<b>'):
              return '[b]';
              break;

            case ($tag == '</strong>' || $tag == '</b>'):
              return '[/b]';
              break;

            case ($tag == '<em>' || $tag == '<i>'):
              return '[i]';
              break;

            case ($tag == '</em>' || $tag == '</i>'):
              return '[/i]';
              break;

            case '<u>':
              return '[u]';
              break;

            case '</u>':
              return '[/u]';
              break;

            case ($tag == '<strike>' || $tag == '<del>'):
              return '[s]';
              break;

            case ($tag == '</strike>' || $tag == '</del>'):
              return '[/s]';
              break;

            case '<ul>':
              return '[list]';
              break;

            case '</ul>':
              return '[/list]';
              break;

            case '<ol>':
              return '[list=1]';
              break;

            case '</ol>':
              return '[/list]';
              break;

            case '<li>':
              return '[*]';
              break;

            case '</li>':
              return '';
              break;

            case '<center>':
              return '[center]';
              break;

            case '</center>':
              return '[/center]';
              break;

            default:
              return $tag;
          }
        },

        $body
      );


      // Now we strip the remaining HTML tags.
      $body = strip_tags($body);


      // Now we can restore the snippets, converting the HTML tags to BBCode tags.
      $snippetsCount = count($snippets[0]);

      for ($i = 0; $i < $snippetsCount; $i++) {
        // We try to determine which tags the code is inside: <pre></pre>, <code></code>, [code][/code]
        if (!empty($snippets['openpre'][$i]))
          $snippet = "[code]".PHP_EOL.trim($snippets['contentpre'][$i]).PHP_EOL."[/code]";
        elseif (!empty($snippets['opencode'][$i]))
          $snippet = "[code]".PHP_EOL.trim($snippets['contentcode'][$i]).PHP_EOL."[/code]";
        else
          $snippet = $snippets['openbbcode'][$i].PHP_EOL.trim($snippets['contentbbcode'][$i]).PHP_EOL.$snippets['closebbcode'][$i];

        $body = preg_replace('/___SNIPPET___/', PHP_EOL.trim($snippet).PHP_EOL, $body, 1);
      }


      // Converts from BBCode to Markdown!

      // Replaces BBCode bold.
      $body = preg_replace_callback('%\[b\]([\W\D\w\s]*?)\[/b\]%i',

        function ($matches) use ($item) {
          return "**".$matches[1]."**";
        },

        $body
      );

      // Replaces BBCode italic.
      $body = preg_replace_callback('%\[i\]([\W\D\w\s]*?)\[/i\]%i',

        function ($matches) use ($item) {
          return "*".$matches[1]."*";
        },

        $body
      );


      // Replaces BBCode underline. Unfortunately Markdown doesn't support underline, so we just revert to normal text.
      $body = preg_replace_callback('%\[u\]([\W\D\w\s]*?)\[/u\]%i',

        function ($matches) use ($item) {
          return $matches[1];
        },

        $body
      );


      // Replaces BBCode strikethrough.
      $body = preg_replace_callback('%\[s\]([\W\D\w\s]*?)\[/s\]%i',

        function ($matches) use ($item) {
          return "~~".$matches[1]."~~";
        },

        $body
      );


      // Replaces BBCode lists.
      $body = preg_replace_callback('%\[list(?P<type>=1)?\](?P<items>[\W\D\w\s]*?)\[/list\]%i',

        function ($matches) use ($item) {
          $buffer = "";

          if (preg_match_all('/(?:(?![[*\]]).)*/i', $matches['items'], $listItems)) {

            if (isset($matches['type']) && $matches['type'] == '=1') { // ordered list
              foreach ($listItems as $itemMatch)
                $buffer .= '- '.trim($itemMatch[1]).PHP_EOL;
            }
            else { // unordered list
              $counter = count($listItems);
              for ($i = 0; $i < $counter; $i++)
                $buffer .= (string)($i + 1).'. '.trim($listItems[$i][1]).PHP_EOL;
            }

          }

          return $buffer;
        },

        $body
      );


      // Replaces BBCode urls.
      $body = preg_replace_callback('%\[url\s*=\s*("(?:[^"]*")|\A[^\']*\Z|(?:[^\'">\s]+))\s*(?:[^]\s]*)\]([\W\D\w\s]*?)\[/url\]%i',

        function ($matches) use ($item) {
          if (isset($matches[1]) && isset($matches[2]))
            return "[".$matches[2]."](".$matches[1].")";
          else
            throw new \RuntimeException(sprintf("Article with idItem = %d have malformed BBCode urls", $item->idItem));
        },

        $body
      );


      // Replaces BBCode images.
      $body = preg_replace_callback('%\[img\s*\]\s*("(?:[^"]*")|\A[^\']*\Z|(?:[^\'">\s]+))\s*(?:[^]\s]*)\[/img\]%i',

        function ($matches) use ($item) {
          if (isset($matches[1]))
            return "![]"."(".$matches[1].")";
          else
            throw new \RuntimeException(sprintf("Article with idItem = %d have malformed BBCode images", $item->idItem));
        },

        $body
      );


      // Replaces BBCode snippets.
      $body = preg_replace_callback('%\[code\s*=?(?P<language>\w*)\](?P<snippet>[\W\D\w\s]*?)\[/code\]%i',

        function ($matches) use ($item) {
          if (isset($matches['snippet']))
            return "```".$matches['language'].PHP_EOL.$matches['snippet']."```".PHP_EOL;
          else
            throw new \RuntimeException(sprintf("Article with idItem = %d have malformed BBCode snippet", $item->idItem));
        },

        $body
      );


      // This is the converted body.
      $article->body = utf8_encode($body);

      // We finally save the article.
      $this->couch->saveDoc($article);

      // We update the article views.
      $this->redis->hSet($article->id, 'hits', $item->hitNum);

      $progress->advance();
    }

    mysqli_free_result($result);

    $progress->finish();
  }


  //! @brief Imports books.
  private function importBooks() {
    $this->output->writeln("Importing books...");

    $sql = "SELECT id, idItem, title, body, UNIX_TIMESTAMP(date) AS unixTime, hitNum, replyNum, stereotype, locked, contributorName, idMember FROM Item WHERE (stereotype = ".self::BOOK.") ORDER BY date DESC";
    $sql .= $this->limit;

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    $rows = mysqli_num_rows($result);
    $progress = $this->getApplication()->getHelperSet()->get('progress');
    $progress->start($this->output, $rows);

    while ($item = mysqli_fetch_object($result)) {
      $book = new Book();

      $book->id = $item->id;
      $book->publishingDate = (int)$item->unixTime;
      $book->title = utf8_encode($item->title);
      $book->creator = utf8_encode($item->contributorName);

      $body = $item->body;

      if (preg_match('/\\[isbn\\](.*?)\\[\/isbn\\]/s', $body, $matches))
        $book->isbn = utf8_encode($matches[1]);
      if (preg_match('/\\[authors\\](.*?)\\[\/authors\\]/s', $body, $matches))
        $book->authors = utf8_encode($matches[1]);
      if (preg_match('/\\[publisher\\](.*?)\\[\/publisher\\]/s', $body, $matches))
        $book->publisher = utf8_encode($matches[1]);
      if (preg_match('/\\[language\\](.*?)\\[\/language\\]/s', $body, $matches))
        $book->language = utf8_encode($matches[1]);
      if (preg_match('/\\[year\\](.*?)\\[\/year\\]/s', $body, $matches))
        $book->year = $matches[1];
      if (preg_match('/\\[pages\\](.*?)\\[\/pages\\]/s', $body, $matches))
        $book->pages = $matches[1];
      if (preg_match('/\\[attachments\\](.*?)\\[\/attachments\\]/s', $body, $matches) && !empty($matches[1]))
        $book->attachments = utf8_encode($matches[1]);
      if (preg_match('/\\[review\\](.*?)\\[\/review\\]/s', $body, $matches))
        $book->body = utf8_encode($matches[1]);
      if (preg_match('/\\[positive\\](.*?)\\[\/positive\\]/s', $body, $matches))
        $book->positive = utf8_encode($matches[1]);
      if (preg_match('/\\[negative\\](.*?)\\[\/negative\\]/s', $body, $matches))
        $book->negative = utf8_encode($matches[1]);

      if (preg_match('/\\[vendorLink\\](.*?)\\[\/vendorLink\\]/s', $body, $matches) && !empty($matches[1]))
        $book->link = utf8_encode($matches[1]);

      $this->couch->saveDoc($book);

      // We update the book views.
      $this->redis->hSet($book->id, 'hits', $item->hitNum);

      $progress->advance();
    }

    mysqli_free_result($result);

    $progress->finish();
  }


  //! @brief Imports tags.
  private function importTags() {
    $this->output->writeln("Importing tags...");

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
      $tag->name = utf8_encode(strtolower(str_replace(" ", "-", $item->name)));

      $this->couch->saveDoc($tag);

      $progress->advance();
    }

    mysqli_free_result($result);

    $progress->finish();
  }


  //! @brief Imports classifications.
  private function importClassifications() {
    $this->output->writeln("Importing classifications...");

    $sql = "SELECT I.id AS itemId, C.id AS tagId, I.stereotype AS stereotype, UNIX_TIMESTAMP(I.date) AS unixTime FROM Item I, Category C, ItemsXCategory X WHERE I.idItem = X.idItem AND C.idCategory = X.idCategory AND (I.stereotype = 2 OR I.stereotype = 11)";
    $sql .= $this->limit;

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    $rows = mysqli_num_rows($result);
    $progress = $this->getApplication()->getHelperSet()->get('progress');
    $progress->start($this->output, $rows);

    while ($item = mysqli_fetch_object($result)) {

      if ($item->stereotype == 2)
        $postType = 'article';
      else
        $postType = 'book';

      $doc = Classification::create($item->itemId, $postType, 'blog', $item->tagId, (int)$item->unixTime);

      $this->couch->saveDoc($doc);

      $progress->advance();
    }

    mysqli_free_result($result);

    $progress->finish();
  }


  //! @brief Imports favourites.
  private function importFavourites() {
    $this->output->writeln("Importing favourites...");

    $sql = "SELECT I.id AS itemId, M.idMember AS userId, UNIX_TIMESTAMP(F.date) as timestamp FROM Item I, Member M, Favourite F WHERE I.idItem = F.idItem AND M.idMember = F.idMember";
    $sql .= $this->limit;

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    $rows = mysqli_num_rows($result);
    $progress = $this->getApplication()->getHelperSet()->get('progress');
    $progress->start($this->output, $rows);

    while ($item = mysqli_fetch_object($result)) {
      $timestamp = (int)$item->timestamp;

      if ($timestamp > 0)
        $doc = Star::create($item->itemId, $item->userId, $timestamp);
      else
        $doc = Star::create($item->itemId, $item->userId);

      $this->couch->saveDoc($doc);

      $progress->advance();
    }

    mysqli_free_result($result);

    $progress->finish();
  }


  //! @brief Imports aggregates.
  private function importAggregates() {
    $this->output->writeln("Importing aggregates...");

    $sql = "SELECT correlationCode, title, UNIX_TIMESTAMP(date) AS unixTime, contributorName, idMember FROM Item WHERE (stereotype = ".self::ARTICLE.") GROUP BY correlationCode HAVING COUNT(correlationCode) > 1 ORDER BY date ASC";
    $sql .= $this->limit;

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    $rows = mysqli_num_rows($result);
    $progress = $this->getApplication()->getHelperSet()->get('progress');
    $progress->start($this->output, $rows);

    while ($item = mysqli_fetch_object($result)) {
      $aggregate = new Aggregate();

      $aggregate->id = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING);
      $aggregate->publishingDate = (int)$item->unixTime;
      $aggregate->title = utf8_encode(rtrim($item->title, '()/123456789 \t\n\r\0\x0B'));
      $aggregate->creator = utf8_encode($item->contributorName);


      $sql = "SELECT id, UNIX_TIMESTAMP(date) AS unixTime, hitNum FROM Item WHERE correlationCode = '".$item->correlationCode."' ORDER BY date ASC";

      $related = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

      $i = 0;
      while ($article = mysqli_fetch_object($related)) {
        $aggregate->addPost($article->id, $i);

        // We update the total aggregate views.
        $this->redis->hIncrBy($aggregate->id, 'hits', $article->hitNum);

        $i++;
      }

      $this->couch->saveDoc($aggregate);

      $progress->advance();
    }

    mysqli_free_result($result);

    $progress->finish();
  }


  //! @brief Imports subscriptions.
  private function importSubscriptions() {
    $this->output->writeln("Importing subscriptions...");

    $sql = "SELECT I.id AS itemId, M.idMember AS userId, UNIX_TIMESTAMP(T.creationTime) as timestamp FROM Item I, Member M, Thread T WHERE I.idItem = T.idItem AND M.idMember = T.idMember";
    $sql .= $this->limit;

    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    $rows = mysqli_num_rows($result);
    $progress = $this->getApplication()->getHelperSet()->get('progress');
    $progress->start($this->output, $rows);

    while ($item = mysqli_fetch_object($result)) {
      $timestamp = (int)$item->timestamp;

      if ($timestamp > 0)
        $doc = Subscription::create($item->itemId, $item->userId, $timestamp);
      else
        $doc = Subscription::create($item->itemId, $item->userId);

      $this->couch->saveDoc($doc);

      $progress->advance();
    }

    mysqli_free_result($result);

    $progress->finish();
  }


  //! @brief Import all entities.
  private function importAll() {
    $this->importUsers();
    $this->importArticles();
    $this->importBooks();
    $this->importTags();
    $this->importClassifications();
    $this->importFavourites();
    $this->importAggregates();
    $this->importSubscriptions();
  }


  //! @brief Configures the command.
  protected function configure() {
    $this->setName("import");
    $this->setDescription("Imports into CouchDB the data from Programmazione.it v6.4 MySQL database.");
    $this->addArgument("entities",
        InputArgument::IS_ARRAY | InputArgument::REQUIRED,
        "The entities you want import. Use 'all' if you want import all the entities, 'users' if you want just import the
        users or separate multiple entities with a space. The available entities are: users, articles, books, tags.");
    $this->addOption("limit",
        NULL,
        InputOption::VALUE_OPTIONAL,
        "Limit the imported articles. This option is applied only when 'articles' argument is provided, alone or as a
        part of the array, else is ignored.");
  }


  //! @brief Executes the command.
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->mysql = $this->_di['mysql'];
    $this->couch = $this->_di['couchdb'];
    $this->redis = $this->_di['redis'];

    $this->input = $input;
    $this->output = $output;

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

          case 'favourites':
            $this->importFavourites();
            break;

          case 'aggregates':
            $this->importAggregates();
            break;

          case 'subscriptions':
            $this->importSubscriptions();
            break;
        }

    }
    else
      $this->importAll();

    $this->couch->ensureFullCommit();
  }

}
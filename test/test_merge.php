<?php

/**
 * @file test.php
 * @brief A test file.
 * @details
 * @author Filippo F. Fadda
 */


use Monolog\Logger;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;

use PitPress\Helper\Text;


$root = realpath(__DIR__."/../");

// Initializes the Composer autoloading system. (Note: We don't use the Phalcon loader.)
require $root . "/vendor/autoload.php";

$monolog = new Logger('pit-press');

// Registers the Monolog error handler to log errors and exceptions.
ErrorHandler::register($monolog);

// Creates a stream handler to log debugging messages.
$monolog->pushHandler(new StreamHandler($root."/log/test.log", Logger::DEBUG));



$text = <<<'EOT'
C# 4.0, alcune novità (1/4)
EOT;




/**
 * @brief Removes from the title the page number.
 */
function purgeTitle($title) {
  $temp = Text::convertCharset(rtrim($title, '\t\n\r\0\x0B'));
  return preg_replace('%\(\d*/\d*\)%iu', '', $temp);
}


/**
 * @brief Get everything after the `: ` sequence of characters.
 */
function getSubtitle($title) {
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
function convertText($text, $id) {
  $utf8 = Text::convertCharset($text);
  $bbcode = Text::htmlToBBCode($utf8, $id);
  return Text::bbcodeToMarkdown($bbcode, $id);
}


$mysql = mysqli_connect('localhost', 'root', 'cathedral') or die(mysqli_error($mysql));
mysqli_select_db($mysql, 'programmazione') or die(mysql_error());

// To avoid a stupid notice.
$body = "";

$sql = "SELECT idItem, I.id AS id, M.id AS userId, contributorName, I.title, I.body, UNIX_TIMESTAMP(date) AS unixTime, hitNum, downloadNum, locked FROM Item I LEFT OUTER JOIN Member M USING (idMember) WHERE correlationCode = '68f48144-e51b-102c-b728-00093d104d4a' ORDER BY date ASC, idItem ASC";

$pages = mysqli_query($mysql, $sql) or die(mysqli_error($mysql));

$finalTitle = "";
$paragraphTitle = "";
$importedArticles = [];
while ($page = mysqli_fetch_object($pages)) {
  $pageBody = convertText($page->body, $page->idItem);
  $title = purgeTitle($page->title);
  $subtitle = getSubtitle($title);

  if (empty($body)) {

    if (!empty($subtitle))
      $finalTitle = rtrim(mb_strstr($title, $subtitle, TRUE, "UTF-8"), ": \t\n\r\0\x0B");
    else
      $finalTitle = $title;

  }
  else {
    $body .= PHP_EOL.PHP_EOL;
  }

  if (!empty($subtitle) && $subtitle != $paragraphTitle) {
    $body .= Text::capitalize($subtitle).PHP_EOL;
    $body .= str_repeat("-", mb_strlen($subtitle)).PHP_EOL.PHP_EOL;
  }

  $body .= $pageBody;

  $paragraphTitle = $subtitle;
}

$finalBody = $body;

$monolog->addNotice(sprintf("Titolo: %s", $finalTitle));

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
[quote=dedalo]Allora, anticipo qualche novità.

Il forum così com'è cesserà di esistere per lasciare il posto ad una piattaforma Q&A alla stackoverflow, alla quale verrà dato ampio rilievo.

[b]HOME PAGE[/b]


[b]I SERVIZI[/b]


[b]FRAMEWORK HTML[/b]


[b]IL FONT[/b]


[b]I TAG[/b]

Attendo i vostri commenti[/quote]

Sembra tutto ben pensato. Non vedo l'ora di vedere il risultato finale e di contribuire in qualche modo (visto che se non sbaglio volevi rendere il tutto open) :-)
EOT;


$text = preg_replace('~\G(?<!^)(?>(\[quote\b[^]]*](?>[^[]++|\[(?!/?quote)|(?1))*\[/quote])|(?<!\[)(?>[^[]++|\[(?!/?quote))+\K)|\[quote\b[^]]*]\K~', '', $text);

// Replaces all the remaining quotes with '> ' characters.
$text = preg_replace_callback('%\[quote\b[^]]*\]((?>[^[]++|\[(?!/?quote))*)\[/quote\]%i',

  function($matches) {
    $temp = preg_replace('/^\s*/mu', '', trim($matches[1]));

    return "> ".$temp.PHP_EOL.PHP_EOL;
  },

  $text
);

$monolog->addNotice($text);

echo $text;

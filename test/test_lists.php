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
Il mondo di oggi va molto in fretta e ogni giorno siamo sommersi da una quantità enorme di informazioni. Come sanno gli esperti di comunicazione, per catturare l'attenzione degli utenti si tende a sintetizzare uno o più concetti in [b]slogan[/b] o [b]sigle[/b], si attaccano etichette e si tende a classificare ogni aspetto dell'attività umana.
Non fa eccezione lo sviluppo del software: molto spesso si attaccano etichette a questa o quella tecnologia, a questo o quell'approccio; altrettanto spesso sono etichette a scopo di [b]marketing[/b], che tendono a catturare l'attenzione dello sviluppatore e talvolta ad instaurare diatribe sulla validità dell'una o dell'altra tecnologia perdendo di vista il perché delle cose.
Ad esempio, nell'ambito dello [url=http://it.wikipedia.org/wiki/Metodologia_agile t=_blank]sviluppo agile[/url] si discute spesso se sia meglio adottare una pratica invece che un'altra, se un certo prodotto aiuta ad essere più agili.
In realtà l'[b]obiettivo[/b] non è essere agile, ma essere aperti e pronti al cambiamento.
Lo scopo non è creare software [b]test-driven[/b], ma realizzare software che sia possibile testare velocemente ed efficacemente.

Questi ragionamenti hanno spinto [url=http://www.codemanship.co.uk/parlezuml/blog/index.php t=_blank]Jason Gorman[/url], fondatore di [url=http://www.codemanship.co.uk/ t=_blank]Codemanship[/url], ad analizzare quali sono i [b]principi fondamentali[/b] a cui deve ispirarsi uno sviluppatore, indipendentemente dalla tecnologia che utilizza e dall'approccio allo sviluppo che preferisce. Il risultato di questa analisi è un libretto di una quarantina di pagine in formato PDF [b]liberamente scaricabile[/b] dal titolo "[url=http://www.codemanship.co.uk/backtobasics.pdf t=_blank]Back to Basics, Hype-free Principles for Software Developers[/url]".

Nel testo [url=http://www.codemanship.co.uk/parlezuml/blog/index.php t=_blank]Gorman[/url] illustra [b]undici principi[/b] che dovrebbero stare alla base di qualsiasi attività di sviluppo software:
[list]
[*]Il software dovrebbe avere [b]obiettivi testabili[/b]
[*]Lo stretto [b]coinvolgimento[/b] del cliente è la chiave del successo di un progetto software
[*]Lo sviluppo di software è un processo di [b]apprendimento[/b]
[*]Iniziare dalle cose [b]più importanti[/b]
[*][b]Comunicare[/b] è l'attività principale
[*]La [b]prevenzione[/b] è (di solito) più economica della cura
[*]Il software che non può essere utilizzato non ha [b]valore[/b]
[*]Le [b]interfacce[/b] servono per comunicare
[*][b]Automatizzare[/b] il lavoro ripetitivo
[*]Far crescere un software complesso partendo da [b]componenti semplici[/b]
[*]Per imparare occorre essere aperti al [b]cambiamento[/b]
[/list]
Al di là delle etichette, dunque, il buon senso e la capacità di comprendere il perché si sta facendo un determinato lavoro sono la chiave per ottenere i migliori risultati.
EOT;


$text = preg_replace_callback('%\[list(?P<type>=1)?\](?P<items>[\W\D\w\s]*?)\[/list\]%iu',

  function ($matches) {
    $buffer = "";

    $list = preg_replace('/\s*$|^\s*/mu', '', $matches['items']);
    if (is_null($list))
      throw new \RuntimeException(sprintf("Text identified by '%d' has malformed BBCode lists", $this->id));

    $items = preg_split('/\[\*\]/u', $list);

    $counter = count($items);

    if (isset($matches['type']) && $matches['type'] == '=1') { // ordered list
      // We start from 1 to discard the first string, in fact, it's empty.
      for ($i = 1; $i < $counter; $i++)
        if (!empty($items[$i]))
          $buffer .= (string)($i).'. '.trim($items[$i]).PHP_EOL;
    }
    else { // unordered list
      // We start from 1 to discard the first string, in fact, it's empty.
      for ($i = 1; $i < $counter; $i++)
        if (!empty($items[$i]))
          $buffer .= '- '.trim($items[$i]).PHP_EOL;
    }

    // We need a like break above the list and another one below.
    if (!empty($buffer))
      $buffer = PHP_EOL.$buffer.PHP_EOL;

    return $buffer;
  },

  $text
);

$monolog->addNotice($text);

echo $text;

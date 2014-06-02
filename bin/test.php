<?php

/**
 * @file test.php
 * @brief This file contains the ${CLASS_NAME} class.
 * @details
 * @author Filippo F. Fadda
 */


$text = <<<EOT
[quote=foo]I really like the movie. [quote=bar]World[quote]ciao ciao[/quote]

[b]War[/b] Z[/quote] It's [b]amazing![/b][/quote]bubu
This is my comment.
[quote]Hello, World[/quote]
This is another comment.
[quote]Bye Bye Baby[/quote]
EOT;



$text = preg_replace('~\G(?<!^)(?>(\[quote\b[^]]*](?>[^[]++|\[(?!/?quote)|(?1))*\[/quote])|(?<!\[)(?>[^[]++|\[(?!/?quote))+\K)|\[quote\b[^]]*]\K~', '', $text);

$text = preg_replace_callback('%\[quote\b[^]]*\]((?>[^[]++|\[(?!/?quote))*)\[/quote\]%i',

  function($matches) {
    return "> ".trim(str_replace(PHP_EOL, '', $matches[1]));
  },

  $text);


echo $text;
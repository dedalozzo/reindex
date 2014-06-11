<?php

/**
 * @file markdown.php
 * @brief Creates an instance of the Markdown parser and returns it.
 * @details
 * @author Filippo F. Fadda
 */


//use PitPress\Render\SyntaxHighlighter;
use Pygmentize\Pygmentize;


// Creates an instance of Redis client and return it.
$di->setShared('markdown',
  function() use ($config) {
    // We no longer need the SyntaxHighlighter class, because Hoedown uses a closure.

    /*
    $render = new SyntaxHighlighter(
        [
          'filter_html' => TRUE,
          'hard_wrap' => TRUE
        ]
    );
    */

    /*
     $markdown = new Sundown\Markdown($render,
        [
          'no_intra_emphasis' => TRUE,
          'tables' => TRUE,
          'fenced_code_blocks' => TRUE,
          'autolink' => TRUE,
          'strikethrough' => TRUE,
          'lax_html_blocks' => TRUE,
          'space_after_headers' => TRUE,
          'superscript' => TRUE
        ]
    );
    */

    // Replaced Sundown with Hoedown.
    // For a description of the predefined constants see: https://github.com/kjdev/php-ext-hoedown#predefined-constants.
    $hoedown = new Hoedown(
        [
          Hoedown::SPACE_HEADERS => TRUE,
          Hoedown::SUPERSCRIPT => TRUE,
          Hoedown::UNDERLINE => TRUE,
          Hoedown::HIGHLIGHT => TRUE,
          Hoedown::SKIP_HTML => TRUE,
          Hoedown::NO_INTRA_EMPHASIS => FALSE
        ]
    );

    $hoedown->addRender("blockcode", function($code, $language) {
       return Pygmentize::highlight($code, $language);
      }
    );

    return $hoedown;
  }
);
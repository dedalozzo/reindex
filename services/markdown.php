<?php

//! @file markdown.php
//! @brief Creates an instance of the Markdown parser and returns it.
//! @details
//! @author Filippo F. Fadda


use PitPress\Render\SyntaxHighlighter;


// Creates an instance of Redis client and return it.
$di->setShared('markdown',
  function() use ($config) {
    $render = new SyntaxHighlighter(
        [
          'filter_html' => TRUE,
          'hard_wrap' => TRUE
        ]
    );

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

    return $markdown;
  }
);
<?php

/**
 * @file markdown.php
 * @brief Creates an instance of the Markdown parser and returns it.
 * @details
 * @author Filippo F. Fadda
 */


use Pygmentize\Pygmentize;


// Creates an instance of Redis client and return it.
$di->setShared('markdown',

  function() use ($config, $monolog) {

    // For a description of the predefined constants see: https://github.com/kjdev/php-ext-hoedown#predefined-constants.
    $hoedown = new Hoedown(
        [
          // HTML.
          Hoedown::RENDERER_HTML => TRUE, // Render HTML.
          Hoedown::RENDERER_TOC	=> FALSE, // Render the Table of Contents in HTML.
          Hoedown::SKIP_HTML => TRUE, // Strip all HTML tags.
          Hoedown::ESCAPE => FALSE, // Escape all HTML.
          //Hoedown::EXPAND_TABS => FALSE, // todo
          Hoedown::HARD_WRAP => FALSE, //	Render each linebreak as <br>.
          Hoedown::USE_XHTML => FALSE, // Render XHTML.
          Hoedown::TASK_LIST => FALSE, // Render task lists.
          Hoedown::LINE_CONTINUE => FALSE, // Render line continue.
          Hoedown::HEADER_ID =>	FALSE, // Render header id.

          // EXT.
          Hoedown::TABLES => TRUE, // Parse PHP-Markdown style tables.
          Hoedown::FENCED_CODE => TRUE, // Parse fenced code blocks.
          Hoedown::FOOTNOTES => FALSE, // Parse footnotes.
          Hoedown::AUTOLINK => TRUE, // Automatically turn URLs into links.
          Hoedown::STRIKETHROUGH => TRUE, // Parse ~~strikethrough~~ spans.
          Hoedown::UNDERLINE => TRUE, // Parse _underline_ instead of emphasis.
          Hoedown::HIGHLIGHT => TRUE, // Parse ==hightlight== spans.
          Hoedown::QUOTE => FALSE, // Render "quotes" as <q>.
          Hoedown::SUPERSCRIPT => TRUE, // Parse super^script.
          //Hoedown::LAX_SPACING => FALSE, // todo
          Hoedown::NO_INTRA_EMPHASIS => FALSE, // Disable emphasis_between_words.
          Hoedown::SPACE_HEADERS => TRUE, // Require a space after '#' in headers.
          Hoedown::DISABLE_INDENTED_CODE => FALSE, // Don't parse indented code blocks.
          Hoedown::SPECIAL_ATTRIBUTE => TRUE, // Parse special attributes.

          // TOC.
          Hoedown::TOC => FALSE, // Produce links to the Table of Contents.
          Hoedown::TOC_BEGIN => 0, // Begin level for headers included in the TOC.
          Hoedown::TOC_END => 6, // End level for headers included in the TOC.
          Hoedown::TOC_ESCAPE => TRUE, // Escape int the TOC.
          Hoedown::TOC_HEADER => "", // Render header in the TOC.
          Hoedown::TOC_FOOTER => "" // Render footer in the TOC.

          //HOEDOWN_OPT_IS_USER_BLOCK, todo
        ]
    );

    $hoedown->addRender("blockcode", function($code, $language) use ($monolog) {
       return Pygmentize::highlight($code, $language);
      }
    );

    return $hoedown;
  }
);
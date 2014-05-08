<?php

//! @file SyntaxHighlighter.php
//! @brief This file contains the SyntaxHighlighter class.
//! @details
//! @author Filippo F. Fadda


//! @brief The Sundown renders namespace.
namespace PitPress\Render;


use Sundown\Render\HTML;
use Pygmentize\Pygmentize;


//! @brief This handler override the blockCode() method of Sundown HTML render, to highlight the source code using
//! Pygmentize class.
//! @deprecated The SyntaxHighlighter is deprecated due to change Sundown with Hoedown.
class SyntaxHighlighter extends HTML {

  public function blockCode($code, $language) {
    $highlightedSource = Pygmentize::highlight($code, $language);

    return $highlightedSource;
  }

} 
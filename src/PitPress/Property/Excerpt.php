<?php

//! @file Excerpt.php
//! @brief This file contains the Excerpt trait.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Property;


//! @cond HIDDEN_SYMBOLS
trait Excerpt {

  //! @brief Cuts a string to a given number of characters without breaking words.
  //! @param[in] string $text The input string.
  //! @param[in] integer $length The number of characters at which the string will be wrapped, ex. 200 characters.
  //! @param[in] string $etc The characters you want append to the end of text.
  //! @param[in] boolean $breakWords If <i>true</i> breaks the words to return the exact number of chars.
  //! @param[in] boolean $middle Truncates the text but remove middle instead the end of the string.
  function truncate($text, $length = 200, $etc = '...', $charset='UTF-8', $breakWords = FALSE, $middle = FALSE) {
    if ($length == 0)
      return '';

    if (mb_strlen($text) > $length) {
      $length -= min($length, mb_strlen($etc));

      if (!$breakWords && !$middle)
        $text = preg_replace('/\s+?(\S+)?$/u', '', mb_substr($text, 0, $length+1, $charset));

      if(!$middle)
        return mb_substr($text, 0, $length, $charset) . $etc;
      else
        return mb_substr($text, 0, $length/2, $charset) . $etc . mb_substr($text, -$length/2, (mb_strlen($text)-$length/2), $charset);
    }
    else
      return $text;
  }


  //! @brief Removes the content of pre tags, than strip all tags.
  //! @param[in] string $text The input string.
  function purge($text) {
    // Removes the content of <pre></pre>.
    $text = preg_replace('/<(pre)(?:(?!<\/\1).)*?<\/\1>/s', '', $text);

    // Removes all the HTML tags.
    $text = strip_tags($text);

    return $text;
  }


  public function getExcerpt() {
    return $this->meta['excerpt'];
  }


  public function issetExcerpt() {
    return isset($this->meta['excerpt']);
  }


  public function setExcerpt($value) {
    $this->meta["excerpt"] = $value;
  }


  public function unsetExcerpt() {
    if ($this->isMetadataPresent('excerpt'))
      unset($this->meta['excerpt']);
  }

}
//! @endcond
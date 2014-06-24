<?php

/**
 * @file Text.php
 * @brief This file contains the Text class.
 * @details
 * @author Filippo F. Fadda
 */


//! PitPress helpers namespace.
namespace PitPress\Helper;


use Converter;


/**
 * @brief This helper class contains routines to process text.
 * @nosubgrouping
 */
class Text {


  /**
   * @brief Converts a string from a charset to another one.
   * @details The default conversion is from `Windows-1252` to `UTF-8`. `Windows-1252` or `CP-1252` is a character
   * encoding of the Latin alphabet, used by default in the legacy components of Microsoft Windows in English and some
   * other Western languages. This character encoding is a superset of `ISO-8859-1`, but it differs from it by using
   * displayable characters rather than control characters in the 80 to 9F (hex) range.
   * @param[in] string $text The input string.
   * @param[in] bool $stripslashes (optional) If `true` strip all the slashes before converting the text.
   * @param[in] string $fromCharset (optional) The origin charset.
   * @param[in] string $toCharset (optional) The target charset.
   * @return string
   * @attention Doesn't matter if the varchar fields of your MySQL tables are encoded in `LATIN1`, in fact, if someone
   * ever posted a document from Windows Word containing smart characters, like curly quotes or smart apostrophes, the
   * real charset use is `Windows-1252`.
   * @warning This function doesn't use `LATIN1` or `ISO-8859-1` as default, because `Windows-1251` and `Windows-1252`
   * will only succeed if the entire string consists of high-byte characters in a certain range. That means you'll never
   * get the right conversion because the text will appear as `ISO-8859-1` even if it is `Windows-1252`. See the bug
   * section.
   * @bug https://bugs.php.net/bug.php?id=64667
   */
  public static function convertCharset($text, $stripslashes = FALSE, $fromCharset = 'Windows-1252', $toCharset = 'UTF-8') {
    if ($stripslashes)
      return iconv($fromCharset, $toCharset, stripslashes($text));
    else
      return iconv($fromCharset, $toCharset, $text);
  }


  /**
   * @brief Cuts a string to a given number of characters without breaking words.
   * @param[in] string $text The input string.
   * @param[in] integer $length The number of characters at which the string will be wrapped, ex. 200 characters.
   * @param[in] string $etc The characters you want append to the end of text.
   * @param[in] string $charset (optional) The charset used.
   * @param[in] bool $breakWords (optional) If `true` breaks the words to return the exact number of chars.
   * @param[in] bool $middle (optional) Truncates the text but remove middle instead the end of the string.
   * @return string
   * @warning This function works with UTF-8 strings.
   */
  public static function truncate($text, $length = 200, $etc = ' ...', $charset='UTF-8', $breakWords = FALSE, $middle = FALSE) {
    if ($length == 0)
      return '';

    if (mb_strlen($text) > $length) {
      $length -= min($length, mb_strlen($etc, $charset));

      if (!$breakWords && !$middle)
        $text = preg_replace('/\s+?(\S+)?$/u', '', mb_substr($text, 0, $length+1, $charset));

      if(!$middle)
        return mb_substr($text, 0, $length, $charset) . $etc;
      else
        return mb_substr($text, 0, $length/2, $charset) . $etc . mb_substr($text, -$length/2, (mb_strlen($text, $charset) - $length/2), $charset);
    }
    else
      return $text;
  }


  /**
   * @brief Capitalizes the given string.
   * @param[in] string $text The input string.
   * @param[in] string $charset (optional) The charset used.
   * @return string
   * @warning This function works with UTF-8 strings.
   */
  public static function capitalize($text, $charset = 'UTF-8') {
    return mb_strtoupper(mb_substr($text, 0, 1, $charset), $charset) . mb_strtolower(mb_substr($text, 1, mb_strlen($text, $charset), $charset), $charset);
  }


  /**
   * @brief Removes the content of pre tags, than strip all tags.
   * @param[in] string $text The input string.
   * @return string
   * @warning This function works with UTF-8 strings.
   */
  public static function purge($text) {
    // Removes the content of <pre></pre>.
    $text = preg_replace('/<(pre)(?:(?!<\/\1).)*?<\/\1>/su', '', $text);

    // Removes all the HTML tags.
    $text = strip_tags($text);

    return $text;
  }


  /**
   * @brief Removes unwanted MS Word smart characters from a string.
   * @param[in] string $text The text to be sanitized.
   * @return string The sanitized text.
   * @warning This function doesn't work with UTF-8 strings.
   */
  public static function sanitize($text) {
    $from = [
      "\xe2\x80\x98", // Left single quote.
      "\xe2\x80\x99", // Right single quote.
      "\xe2\x80\x9c", // Left double quote.
      "\xe2\x80\x9d", // Right double quote.
      "\xe2\x80\x94", // Em dash.
      "\xe2\x80\xa6" // Elipses.
    ];

    $to = [
      "'",
      "'",
      '"',
      '"',
      '&mdash;',
      '...'
    ];

    return htmlspecialchars(str_replace($from, $to, $text));
  }


  /**
   * @brief Converts the given text from HTML to BBCode.
   * @param[in] string $text The text to be sanitized.
   * @param[in] string $id (optional) In case of error, use the given ID in the exception message.
   * @return string The sanitized text.
   * @warning This function works with UTF-8 strings.
   */
  public static function htmlToBBCode($text, $id = "") {
    $converter = new Converter\HTMLConverter($text, $id);
    return $converter->toBBCode();
  }


  /**
   * @brief Converts the given text from BBCode to Markdown.
   * @param[in] string $text The text to be sanitized.
   * @param[in] string $id (optional) In case of error, use the given ID in the exception message.
   * @return string The sanitized text.
   * @warning This function works with UTF-8 strings.
   */
  public static function bbcodeToMarkdown($text, $id = "") {
    $converter = new Converter\BBCodeConverter($text, $id);
    return $converter->toMarkdown();
  }

} 
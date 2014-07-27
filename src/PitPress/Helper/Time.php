<?php

/**
 * @file Time.php
 * @brief This file contains the Time class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Helper;


use ElephantOnCouch\Helper\TimeHelper;


/**
 * @brief This class extends the ElephantOnCouch TimeHelper, adding new methods.
 * @nosubgrouping
 */
class Time extends TimeHelper {

  private static $periods = ['sempre', 'anno', 'trimestre', 'mese', 'settimana', '24-ore'];

  private static $months = [
    'gennaio',
    'febbraio',
    'marzo',
    'aprile',
    'maggio',
    'giugno',
    'luglio',
    'agosto',
    'settembre',
    'ottobre',
    'novembre',
    'dicembre'
  ];


  /**
   * @brief Returns a measure of the time passed since the provided timestamp. In case is passed more than a day,
   * returns a human readable date.
   * @param[in] string $timestamp A timestamp in seconds.
   * @param[in] bool $showTime When `true` returns also the time passed in case of an event occurred in the past.
   * @return string
   */
  public static function when($timestamp, $showtime = TRUE) {
    $today = date('Ymd');

    // Today.
    if ($today == date('Ymd', $timestamp)) {
      $time = TimeHelper::since($timestamp);

      if ($time['hours'] > 1)
        return sprintf('%d ore fa', $time['hours']);
      elseif ($time['hours'] == 1)
        return "un'ora fa";
      elseif ($time['minutes'] > 1)
        return sprintf('%d minuti fa', $time['minutes']);
      elseif ($time['minutes'] == 1)
        return "un minuto fa";
      elseif ($time['seconds'] > 1)
        return sprintf('%d secondi fa', $time['seconds']);
      else // $time['seconds'] == 1
        return "un secondo fa";
    }
    // Yesterday.
    elseif (strtotime('-1 day', $today) == date('Ymd', $timestamp))
      return "ieri";
    // In the past.
    else
      return $showtime ? date('d/m/Y H:i', $timestamp) : date('d/m/Y', $timestamp);
  }


  /**
   * @brief Returns an array of periods.
   * @param[in] integer $count The number of periods from left to right.
   * @return array
   */
  public static function periods($number = NULL) {
    return array_slice(self::$periods, 0, $number, TRUE);
  }


  /**
   * @brief Given a period as string, returns the position in periods array.
   * @param[in] string $period A period of time.
   * @return int|bool The position or `false` in case the period doesn't exist.
   */
  public static function periodIndex($period) {
    $temp = array_flip(self::$periods);

    if (array_key_exists($period, $temp))
      return $temp[$period];
    else
      return FALSE;
  }


  /**
   * @brief Returns all the months in a year in reverse order.
   * @return array
   */
  public static function months() {
    return array_reverse(self::$months);
  }


  /**
   * @brief Given a month as string, returns the his number as string. For example, given `maggio` it returns `05`.
   * @param[in] string $month A month.
   * @return string|bool A double digit month number as string or `false` in case the month doesn't exist.
   */
  public static function monthIndex($month) {

    $temp = array_flip(self::$months);

    if (array_key_exists($month, $temp))
      return sprintf("%02s", $temp[$month]);
    else
      return FALSE;
  }


  /**
   * @brief Given a period as string, returns a timestamp since now in the past.
   * @param[in] string $period A period of time.
   * @return int
   */
  public static function timestamp($period) {
    switch ($period) {
      case '24-ore':
        $timestamp = strtotime('-1 day');
        break;
      case 'settimana':
        $timestamp = strtotime('-1 week');
        break;
      case 'mese':
        $timestamp = strtotime('-1 month');
        break;
      case 'trimestre':
        $timestamp = strtotime('-3 month');
        break;
      case 'anno':
        $timestamp = strtotime('-1 year');
        break;
      default:
        $timestamp = new \stdClass();
    }

    return $timestamp;
  }

}
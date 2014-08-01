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

  /** @name Time Periods */
  //!@{
  const DAY = 5;
  const WEEK = 4;
  const MONTH = 3;
  const QUARTER = 2;
  const YEAR = 1;
  const EVER = 0;
  //!@}


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
   * @brief Given a period as string, returns the timestamp of that past time.
   * @param[in] string $period A period of time.
   * @return int
   */
  public static function aWhileBack($period) {
    switch ($period) {
      case self::DAY:
        $timestamp = strtotime('-1 day');
        break;
      case self::WEEK:
        $timestamp = strtotime('-1 week');
        break;
      case self::MONTH:
        $timestamp = strtotime('-1 month');
        break;
      case self::QUARTER;
        $timestamp = strtotime('-3 month');
        break;
      case self::YEAR:
        $timestamp = strtotime('-1 year');
        break;
      default:
        $timestamp = new \stdClass();
    }

    return $timestamp;
  }

}
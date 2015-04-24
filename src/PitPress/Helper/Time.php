<?php

/**
 * @file Time.php
 * @brief This file contains the Time class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Helper;


use EoC\Helper\TimeHelper;


/**
 * @brief This class extends the Elephant on Couch TimeHelper, adding new methods.
 * @nosubgrouping
 */
class Time extends TimeHelper {

  /** @name Time Periods */
  //!@{
  const TODAY = 0;
  const YESTERDAY = 1;
  const THIS_WEEK = 2;
  const LAST_WEEK = 3;
  const THIS_MONTH = 4;
  const LAST_MONTH = 5;
  const THIS_YEAR = 6;
  const LAST_YEAR = 7;
  const EVER = 8;
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
   * @brief Given a constant representing a period, returns a formatted string.
   * @param[in] int $periodInTime A period in time.
   * @param[in] string $prefix A string prefix.
   * @param[in] string $prefix A string postfix.
   * @return string
   */
  public static function aWhileBack($periodInTime, $prefix = "", $postfix = "") {
    $date = new \DateTime();

    switch ($periodInTime) {
      case self::TODAY:
        $format = $date->format("Ymd");
        break;
      case self::YESTERDAY:
        $date->modify('yesterday');
        $format = $date->format("Ymd");
        break;
      case self::THIS_WEEK:
        $format = $date->format("Y_W");
        break;
      case self::LAST_WEEK;
        $date->modify('last week');
        $format = $date->format("Ymd");
        break;
      case self::THIS_MONTH;
        $format = $date->format("Ym");
        break;
      case self::LAST_MONTH;
        $date->modify('last month');
        $format = $date->format("Ym");
        break;
      case self::THIS_YEAR;
        $format = $date->format("Y");
        break;
      case self::LAST_YEAR:
        $date->modify('last year');
        $format = $date->format("Y");
        break;
      default: // EVER
        $format = "";
    }

    return empty($format) ? $format : $prefix.$format.$postfix;
  }


  /**
   * @brief Given a period of time (an year, a month or a day), calculates the date limits for that period.
   * @param[out] \DateTime $minDate The minimum date in the period.
   * @param[out] \DateTime $maxDate The maximum date in the period.
   * @param[in] string $year An year.
   * @param[in] string $month A month.
   * @param[in] string $day A day.
   */
  public static function dateLimits(&$minDate, &$maxDate, $year, $month = NULL, $day = NULL) {
    $aDay = (is_null($day)) ? 1 : (int)$day;
    $aMonth = (is_null($month)) ? 1 : (int)$month;
    $aYear = (int)$year;

    $minDate = (new \DateTime())->setDate($aYear, $aMonth, $aDay)->modify('midnight');
    $maxDate = clone($minDate);

    if (isset($day))
      $maxDate->modify('tomorrow')->modify('last second');
    elseif (isset($month))
      $maxDate->modify('last day of this month')->modify('last second');
    else
      $maxDate->setDate($aYear, 12, 31)->modify('last second');
  }

}
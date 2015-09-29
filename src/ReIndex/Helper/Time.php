<?php

/**
 * @file Time.php
 * @brief This file contains the Time class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Helper;


use EoC\Helper\TimeHelper;


/**
 * @brief This class extends the Elephant on Couch TimeHelper, adding new methods.
 * @nosubgrouping
 */
class Time extends TimeHelper {

  /** @name Time Periods */
  //!@{
  const TODAY = "today";
  const YESTERDAY = "yesterday";
  const THIS_WEEK = "this-week";
  const LAST_WEEK = "last-week";
  const THIS_MONTH = "this-month";
  const LAST_MONTH = "last-month";
  const THIS_YEAR = "this-year";
  const LAST_YEAR = "last-year";
  const ALL_TIME = "all-time";
  //!@}


  /** @name Time Periods Array */
  //!@{
  public static $periods = [
    self::ALL_TIME => NULL,
    self::THIS_YEAR => NULL,
    self::LAST_YEAR => NULL,
    self::THIS_MONTH => NULL,
    self::LAST_MONTH => NULL,
    self::THIS_WEEK => NULL,
    self::LAST_WEEK => NULL,
    self::TODAY => NULL,
    self::YESTERDAY => NULL
  ];
  //!@}


  /**
   * @brief Checks if the provided string represents a period of time and returns it if exists otherwise returns `false`.
   * @param[in] string $str A human readable period of time.
   * @retval int|bool If the period of time exists returns it, else returns `false`. In case `$str` is `null`, returns
   * `all_time`.
   */
  public static function period($str) {
    return is_null($str) ? self::ALL_TIME : ArrayHelper::key($str, self::$periods);
  }


  /**
   * @brief Returns a measure of the time passed since the provided timestamp. In case is passed more than a day,
   * returns a human readable date.
   * @param[in] int $timestamp A timestamp in seconds.
   * @param[in] bool $showTime When `true` returns also the time passed in case of an event occurred in the past.
   * @retval string
   */
  public static function when($timestamp, $showTime = TRUE) {
    $today = date('Ymd');

    // Today.
    if ($today == date('Ymd', $timestamp)) {
      $time = TimeHelper::since($timestamp);

      if ($time['hours'] > 1)
        return sprintf('%d hours ago', $time['hours']);
      elseif ($time['hours'] == 1)
        return "one hour ago";
      elseif ($time['minutes'] > 1)
        return sprintf('%d minutes ago', $time['minutes']);
      elseif ($time['minutes'] == 1)
        return "one minute fa";
      elseif ($time['seconds'] > 1)
        return sprintf('%d seconds ago', $time['seconds']);
      else // $time['seconds'] == 1
        return "one second ago";
    }
    // Yesterday.
    elseif (strtotime('-1 day', $today) == date('Ymd', $timestamp))
      return "yesterday";
    // In the past.
    else
      return $showTime ? date('d/m/Y H:i', $timestamp) : date('d/m/Y', $timestamp);
  }


  /**
   * @brief Given a constant representing a period, returns a formatted string.
   * @param[in] int $periodInTime A period in time.
   * @param[in] string $prefix A string prefix.
   * @param[in] string $postfix A string postfix.
   * @retval string
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
   * @param[in] string $month (optional) A month.
   * @param[in] string $day (optional) A day.
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
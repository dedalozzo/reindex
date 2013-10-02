<?php

//! @file Time.php
//! @brief This file contains the Time class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Helper;


use ElephantOnCouch\Helper\TimeHelper;


//! This class extends the ElephantOnCouch TimeHelper, adding new methods.
class Time extends TimeHelper {

  private static $periods = ['sempre', 'anno', 'trimestre', 'mese', 'settimana', '24-ore'];


  //! @brief Returns a measure of the time passed since timestamp. In case is passed more than a day, returns a human
  //! readable date.
  //! @param[in] string $timestamp A timestamp in seconds.
  //! @return string
  public static function when($timestamp, $showtime = TRUE) {
    $today = date('Ymd');

    // Today.
    if ($today == date('Ymd', $timestamp)) {
      $time = TimeHelper::since($timestamp);

      if ($time['hours'] == 1)
        return "un'ora fa";
      elseif ($time['hours'] > 1)
        return sprintf('$d ore fa', $time['hours']);
      elseif ($time['minutes'] == 1)
        return "un minuto fa";
      elseif ($time['minutes'] > 1)
        return sprintf('$d minuti fa', $time['minutes']);
      elseif ($time['seconds'] == 1)
        return "un secondo fa";
      else // $time['seconds'] > 1
        return sprintf('$d secondi fa', $time['seconds']);
    }
    // Yesterday.
    elseif (strtotime('-1 day', $today) == date('Ymd', $timestamp))
      return "ieri";
    // In the past.
    else
      return $showtime ? date('d/m/Y H:i', $timestamp) : date('d/m/Y', $timestamp);
  }


  //! @brief Returns an array of periods.
  //! @param[in] integer $count The number of periods from left to right.
  //! @return array
  public static function periods($number = NULL) {
    return array_slice(self::$periods, 0, $number, TRUE);
  }


  //! @brief Given a period as string, returns the position in periods array.
  //! @param[in] string $period A period of time.
  //! @return integer
  public static function periodIndex($period) {
    return array_flip(self::$periods)[$period];
  }


  //! @brief Given a period as string, returns a timestamp since now in the past.
  //! @param[in] string $period A period of time.
  //! @return integer
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
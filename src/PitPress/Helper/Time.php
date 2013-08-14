<?php

//! @file Time.php
//! @brief This file contains the Time class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Helper;


use ElephantOnCouch\Helper\TimeHelper;


//! This class extends the ElephantOnCouch TimeHelper, adding new methods.
class Time extends TimeHelper {

  //! @brief Returns a measure of the time passed since timestamp. In case is passed more than a day, returns a human
  //! readable date.
  //! @param[in] string $timestamp A timestamp in seconds.
  //! @return string
  public static function when($timestamp) {
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
      elseif ($time['seconds'] > 1)
        return sprintf('$d secondi fa', $time['seconds']);
    }
    // Yesterday.
    elseif (strtotime('-1 day', $today) == date('Ymd', $timestamp)) {
      return "ieri";
    }
    // In the past.
    else {
      return date('d/m/Y H:i', $timestamp);
    }
  }

} 
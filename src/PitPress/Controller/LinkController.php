<?php

//! @file LinkController.php
//! @brief This file contains the LinkController class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Controller;


use PitPress\Helper;


/**
 * @brief Controller of Link actions.
 * @nosubgrouping
 */
class LinkController extends IndexController {


  protected function getLabel() {
    return 'links';
  }


  protected function getPeriod($filter) {
    return empty($filter) ? Helper\Time::DAY : Helper\ArrayHelper::value($filter, $this->periods);
  }

}
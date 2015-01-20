<?php

//! @file IConsumer.php
//! @brief This file contains the IConsumer class.
//! @details
//! @author Filippo F. Fadda


//! OAuth service providers namespace.
namespace PitPress\Security\Consumer;


use PitPress\Model\User;


/**
 * @brief This interface defines common methods between every class who represent an OAuth Service Provider.
 * @nosubgrouping
 */
interface IConsumer {


  /**
   * @brief Assigns the retrieved metadata to the user.
   * @param[in] User $user The user instance.
   */
  function assignTo(User $user);

}
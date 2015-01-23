<?php

/**
 * @file UserNotVerifiedException.php
 * @brief This file contains the UserNotVerifiedException class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Exception;


/**
 * @brief Exception thrown when the user hasn't verified yet the e-mail address.
 */
class UserNotVerifiedException extends \RuntimeException {}
<?php

/**
 * @file NotEnoughPrivilegesException.php
 * @brief This file contains the NotEnoughPrivilegesException class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Exception;


/**
 * @brief Exception thrown when there is an user registered with the same e-mail.
 */
class NotEnoughPrivilegesException extends \RuntimeException {}
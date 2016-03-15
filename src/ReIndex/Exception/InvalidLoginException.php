<?php

/**
 * @file InvalidLoginException.php
 * @brief This file contains the InvalidLoginException class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Exception;


/**
 * @brief Exception thrown when there is an user registered with the same login.
 */
class InvalidLoginException extends \RuntimeException {}
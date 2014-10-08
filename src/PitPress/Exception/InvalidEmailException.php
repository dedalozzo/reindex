<?php

/**
 * @file InvalidEmailException.php
 * @brief This file contains the InvalidEmailException class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Exception;


/**
 * @brief Exception thrown when there is an user registered with the same e-mail.
 */
class InvalidEmailException extends \RuntimeException {}
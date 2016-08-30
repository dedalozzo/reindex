<?php

/**
 * @file AccessDeniedException.php
 * @brief This file contains the AccessDeniedException class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Exception;


/**
 * @brief Exception thrown when the user doesn't have the permission to access a resource.
 */
class AccessDeniedException extends \RuntimeException {}
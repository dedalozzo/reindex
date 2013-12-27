<?php

//! @file WrongPasswordException.php
//! @brief This file contains the WrongPasswordException class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Exception;


//! @brief Exception thrown when the provided password doesn't match the password inserted during the sign up procedure.
class WrongPasswordException extends \RuntimeException {}
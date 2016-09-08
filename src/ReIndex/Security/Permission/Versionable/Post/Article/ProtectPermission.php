<?php

/**
 * @file Article/ProtectPermission.php
 * @brief This file contains the ProtectPermission class.
 * @details
 * @author Filippo F. Fadda
 */


//! Posts related permissions
namespace ReIndex\Security\Permission\Versionable\Post\Article;


use ReIndex\Security\Permission\Versionable\Post\ProtectPermission as Superclass;


/**
 * @brief Permission to close or lock an article.
 * @nosubgrouping
 */
class ProtectPermission extends Superclass {}
<?php

/**
 * @file Article.php
 * @brief This file contains the Article class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model;


use PitPress\Extension;
use PitPress\Property;


/**
 * @brief This class represents a blog article.
 * @nosubgrouping
 */
class Article extends Post {
  use Property\TExcerpt;
  use Property\TBody;

}
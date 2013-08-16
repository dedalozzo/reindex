<?php

//! @file Article.php
//! @brief This file contains the Article class.
//! @details
//! @author Filippo F. Fadda


//! @brief PitPress blog entries namespace.
namespace PitPress\Model\Blog;


use PitPress\Model\Post;
use PitPress\Extension;
use PitPress\Property;
use PitPress\Enum\PostState;


//! @brief This class represents a blog article.
//! @nosubgrouping
class Article extends Post implements Extension\IModerate {
  use Extension\TModerate;
  use Property\TExcerpt;
  use Property\TBody;


  public function getSection() {
    return 'blog';
  }


  public function getPublishingType() {
    return 'ARTICOLO';
  }


  public function getComments() {

  }


  public function getPages() {

  }

}
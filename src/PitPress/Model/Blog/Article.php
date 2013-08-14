<?php

//! @file Article.php
//! @brief This file contains the Article class.
//! @details
//! @author Filippo F. Fadda


//! @brief PitPress blog entries namespace.
namespace PitPress\Model\Blog;


use PitPress\Model\ModeratedPost;
use PitPress\Property;


//! @brief This class represents a journal's article.
//! @nosubgrouping
class Article extends ModeratedPost {
  use Property\Excerpt;
  use Property\Body;

  //! @name Alternative states an article may assume.
  //@{
  const DRAFT_STATE = "draft"; //!< The article can be saved as draft.
  //@}


  public function getSection() {
    return 'blog';
  }


  public function getPublishingType() {
    return 'ARTICOLO';
  }


  //! @brief Marks the item as draft.
  //! @details When a user works on an article, he wants save many time the item before submit it for publishing.
  public function markAsDraft() {
    $this->meta['state'] = self::DRAFT_STATE;
    $this->save();
  }


  //! @brief Marks the item as important, so the item should be always visible.
  public function pin() {
    if ($this->isPublished) {
      $this->meta['pinned'] = TRUE;
      $this->save();
    }
  }


  //! @brief Reverts the item to the normal state.
  public function unpin() {
    $this->meta['pinned'] = FALSE;
    $this->save();
  }


  //! @brief Returns <i>true</i> if the item has been pinned.
  public function isPinned() {
    return $this->meta["pinned"];
  }


  //! @brief
  public function close() {

  }


  //! @brief
  public function reopen() {

  }


  //! @brief Returns <i>true</i> if any user can't post comments or answers.
  public function isClosed() {
    return $this->meta["closed"];
  }


  public function getComments() {

  }


  public function getPages() {

  }

}
<?php

/**
 * @file Article.php
 * @brief This file contains the Article class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Model;


use ReIndex\Enum;
use ReIndex\Exception;
use ReIndex\Helper\Text;


/**
 * @brief This class represents a blog article.
 * @nosubgrouping
 */
class Article extends Post {

  const INDEX = TRUE; //!< An article post appears on the home page.


  public function save($deferred = FALSE) {
    parent::save($deferred);
  }


  /**
   * @brief Returns `true` if the post can be marked as draft, `false` otherwise.
   * @retval bool
   */
  public function canBeMarkedAsDraft() {
    if ($this->isDraft()) return FALSE;

    if ($this->isCreated() && $this->user->match($this->creatorId))
      return TRUE;
    else
      return FALSE;
  }


  /**
   * @brief Marks the document as draft.
   * @details When a user works on an article, he wants save many time the item before submit it for peer revision.
   */
  public function markAsDraft() {
    $this->meta['status'] = Enum\DocStatus::DRAFT;

    // Used to group by year, month and day.
    $this->meta['year'] = date("Y", $this->createdAt);
    $this->meta['month'] = date("m", $this->createdAt);
    $this->meta['day'] = date("d", $this->createdAt);

    $this->meta['slug'] = Text::slug($this->title);
  }

}
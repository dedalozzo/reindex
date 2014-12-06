<?php

/**
 * @file Article.php
 * @brief This file contains the Article class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model;


use PitPress\Enum;


/**
 * @brief This class represents a blog article.
 * @nosubgrouping
 */
class Article extends Post {


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

    $this->meta['slug'] = $this->buildSlug();
  }


  /**
   * @brief Returns `true` if this document is only a draft, `false` otherwise.
   * @return bool
   */
  public function isDraft() {
    return ($this->isMetadataPresent('status') && $this->getStatus() == DocStatus::DRAFT) ? TRUE : FALSE;
  }


}
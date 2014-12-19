<?php

/**
 * @file Article.php
 * @brief This file contains the Article class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Model;


use PitPress\Enum;
use PitPress\Exception;


/**
 * @brief This class represents a blog article.
 * @nosubgrouping
 */
class Article extends Post {


  public function save($deferred = FALSE) {
    parent::save();
  }


  /**
   * @brief Marks the document as draft.
   * @details When a user works on an article, he wants save many time the item before submit it for peer revision.
   */
  public function markAsDraft() {
    if ($this->guardian->isGuest()) throw new Exception\NoUserLoggedInException('Nessun utente loggato nel sistema.');
    if ($this->isDraft()) return;

    if ($this->hasBeenCreated() && ($this->userId == $this->guardian->getCurrentUser()->id)) {
      $this->meta['status'] = Enum\DocStatus::DRAFT;

      // Used to group by year, month and day.
      $this->meta['year'] = date("Y", $this->createdAt);
      $this->meta['month'] = date("m", $this->createdAt);
      $this->meta['day'] = date("d", $this->createdAt);

      $this->meta['slug'] = $this->buildSlug();
    }
    else
      throw new Exception\IncompatibleStatusException("Stato incompatible con l'operazione richiesta.");
  }

}
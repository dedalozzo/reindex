<?php

/**
 * @file Article.php
 * @brief This file contains the Article class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use ReIndex\Enum\State;
use ReIndex\Helper\Text;
use ReIndex\Security\Role;
use Reindex\Exception;
use ReIndex\Task\IndexPostTask;


/**
 * @brief This class represents a blog article.
 * @nosubgrouping
 */
class Article extends Post {


  public function save() {
    parent::save();
  }


  /**
   * @copydoc Versionable::submit()
   * @details Any modification, even by the same author who wrote the article, must go through the peer review procedure.
   */
  public function submit() {
    if (!$this->user->has(new Role\MemberRole\SubmitRevisionPermission($this)))
      throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");

    $this->state->set(State::SUBMITTED);
    $this->save();
  }


  /**
   * @brief Marks the document as draft.
   * @details When a user works on an article, he wants save many time the item before submit it for peer revision.
   */
  public function saveAsDraft() {
    if (!$this->user->has(new Role\MemberRole\MarkArticleAsDraftPermission($this)))
      throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");

    $this->state->set(State::DRAFT);

    // Used to group by year, month and day.
    $this->meta['year'] = date("Y", $this->createdAt);
    $this->meta['month'] = date("m", $this->createdAt);
    $this->meta['day'] = date("d", $this->createdAt);

    $this->save();
  }

}
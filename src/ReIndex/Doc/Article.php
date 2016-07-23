<?php

/**
 * @file Article.php
 * @brief This file contains the Article class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use ReIndex\Enum\State;
use ReIndex\Security\Role;
use Reindex\Exception;


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

    // In case this is a revision of a published version, we must update the editor identifier.
    if ($this->state->is(State::CURRENT))
      $this->editorId = $this->user->id;

    $this->state->set(State::SUBMITTED);

    $this->save();
  }


  /**
   * @brief Marks the document as draft.
   * @details When a user works on an article, he wants save many time the item before submit it for peer revision.
   */
  public function saveAsDraft() {
    if (!$this->user->has(new Role\MemberRole\SaveAsDraftPermission($this)))
      throw new Exception\NotEnoughPrivilegesException("Privilegi insufficienti o stato incompatibile.");

    $this->state->set(State::DRAFT);

    // Used to group by year, month and day.
    $this->meta['year'] = date("Y", $this->createdAt);
    $this->meta['month'] = date("m", $this->createdAt);
    $this->meta['day'] = date("d", $this->createdAt);

    $this->save();
  }

}
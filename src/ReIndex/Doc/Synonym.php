<?php

/**
 * @file Synonym.php
 * @brief This file contains the Synonym class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use ReIndex\Enum\State;


/**
 * @brief A synonym of a tag.
 * @nosubgrouping
 */
final class Synonym extends ActiveDoc {

  private $state;
  private $relatedIds;


  public function __construct() {
    parent::__construct();
    $this->relatedIds = [];
    $this->state = new State($this->meta);
    $this->state->set(State::CURRENT);
  }


  /**
   * @brief Returns a list of synonyms' related IDs.
   * @retval array An array of IDs.
   * @nosubgrouping
   */
  public function getRelatedIds() {
    return $this->relatedIds;
  }


  /**
   * @brief Assigns a list of synonyms' related IDs.
   * @param[in] array $ids An array of IDs.
   * @nosubgrouping
   */
  public function setRelatesIds(array $ids) {
    $this->relatedIds = $ids;
  }


  public function delete() {
    $this->state->set(State::DELETED);
  }


  //! @cond HIDDEN_SYMBOLS

  public function getName() {
    return $this->meta['name'];
  }


  public function issetName() {
    return isset($this->meta['name']);
  }


  public function setName($value) {
    // A tag name can't be changed unless the tag has never been approved.
    if ($this->state->isCreated())
      $this->meta['name'] = $value;
    else
      throw new \RuntimeException("Il nome di un tag non può essere cambiato.");
  }


  public function unsetName() {
    if ($this->isMetadataPresent('name'))
      unset($this->meta['name']);
  }

  //! @endcond

}
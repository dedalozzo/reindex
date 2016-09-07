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
 *
 * @cond HIDDEN_SYMBOLS
 *
 * @property string $name
 *
 * @property string $creatorId
 *
 * @endcond
 *
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
   * @copydoc ActiveDoc::getDbName()
   */
  protected function getDbName() {
    return 'tags';
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


  /**
   * @brief Deletes the synonym.
   */
  public function delete() {
    $this->state->set(State::DELETED);
    $this->save();
  }


  /**
   * @copydoc ActiveDoc::save()
   */
  public function save($update = TRUE) {
    $userId = $this->user->getId();

    // Creator ID has not been provided.
    if (!isset($this->creatorId) && isset($userId))
      $this->creatorId = $userId;

    parent::save($update);
  }


  //! @cond HIDDEN_SYMBOLS

  public function getName() {
    return $this->meta['name'];
  }


  public function issetName() {
    return isset($this->meta['name']);
  }


  public function setName($value) {
    $this->meta['name'] = $value;
  }


  public function unsetName() {
    if ($this->isMetadataPresent('name'))
      unset($this->meta['name']);
  }


  public function getCreatorId() {
    return $this->meta["creatorId"];
  }


  public function issetCreatorId() {
    return isset($this->meta['creatorId']);
  }


  public function setCreatorId($value) {
    $this->meta["creatorId"] = $value;
  }


  public function unsetCreatorId() {
    if ($this->isMetadataPresent('creatorId'))
      unset($this->meta['creatorId']);
  }

  //! @endcond

}
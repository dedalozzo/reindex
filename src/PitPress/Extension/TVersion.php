<?php

/**
 * @file TVersion.php
 * @brief This file contains the TVersion trait.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Extension;


use ElephantOnCouch\Generator\UUID;


/**
 * @brief Implements IVersion interface.
 */
trait TVersion {


  public static function createVersion($id = NULL) {
    $instance = new self();

    if (is_null($id))
      $instance->meta['_id'] = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING).IVersion::SEPARATOR.(string)microtime();
    else
      $instance->meta['_id'] = (string)$id.IVersion::SEPARATOR.(string)microtime();

    return $instance;
  }


  public function getUnversionId() {
    return strtok($this->meta['_id'], IVersion::SEPARATOR);
  }


  public function getVersion() {
    return substr($this->meta['_id'], stripos($this->meta['_id'], IVersion::SEPARATOR) + strlen(IVersion::SEPARATOR));
  }


  public function issetVersion() {
    if (stripos($this->meta['_id'], IVersion::SEPARATOR) === FALSE)
      return FALSE;
    else
      return TRUE;
  }


  public function setVersion($value) {
    $this->meta['_id'] = $this->getUnversionId().IVersion::SEPARATOR.(string)$value;
  }


  public function unsetVersion() {
    $this->meta['_id'] = $this->getUnversionId();
  }


  public function getChanges() {
  }


  public function rollback($version) {
  }

} 
<?php

/**
 * @file IVersion.php
 * @brief This file contains the IVersion interface.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Extension;


/**
 * @brief Defines control versioning methods.
 * @nosubgrouping
 */
interface IVersion {

  const SEPARATOR = '::';


  /** @name Control Versioning Methods */
  //!@{

  /**
   * @brief Creates an instance of the class, modifying opportunely the ID, appending a version number.
   * @details Versioned items, in fact, share the same ID, but a version number is added to differentiate them.
   * @param[in] string $id When provided use it appending the version number, else a new ID is generated.
   * @return object
   */
  public static function createVersion($id = NULL);


  /**
   * @brief Returns the item ID deprived by the related version.
   * @return string
   */
  public function getUnversionId();


  /**
   * @brief Returns the item version.
   * @return string
   */
  public function getVersion();


  /**
   * @brief Returns `true` if a version number has been provided, otherwise `false`.
   * @return bool
   */
  public function issetVersion();


  /**
   * @brief Appends the version number to the item ID.
   * @param[in] int $value The version number.
   */
  public function setVersion($value);


  /**
   * @brief Removes the version number from the item ID, if any.
   */
  public function unsetVersion();


  /**
   * @brief Retrieves the list of changes.
   */
  public function getChanges();


  /**
   * @brief Rollbacks to the specified version.
   */
  public function rollback($version);

  //!@}

} 
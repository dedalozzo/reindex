<?php

/**
 * @file Starrable.php
 * @brief This file contains the Starrable class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Feature;


/**
 * @brief Defines a common interface for all the objects supporting a starring mechanism.
 * @nosubgrouping
 */
interface Starrable {


  /**
   * @brief Gets the document identifier.
   * @retval string
   */
  function getId();


  /**
   * @brief Gets the document type.
   * @retval string
   */
  function getType();


  /**
   * @brief Checks the document for the given attribute.
   * @retval bool
   */
  function isMetadataPresent();


  /**
   * @brief Returns the metadata.
   * @param[in] string $name The attribute name.
   * @retval mixed
   */
  function getMetadata($name);

}
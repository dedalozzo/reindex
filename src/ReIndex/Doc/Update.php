<?php

/**
 * @file Update.php
 * @brief This file contains the Update class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use ReIndex\Security\Permission\Revision\Post\Update as Permission;
use Reindex\Exception;


/*
 * @brief This class represents an user's update.
 * @nosubgrouping
 */
class Update extends Post {


  /**
   * @copydoc Post::close()
   */
  public function close() {
    if (!$this->user->has(new Permission\ProtectPermission($this)))
      throw new Exception\AccessDeniedException("Privilegi insufficienti o stato incompatibile.");

    $this->protect(self::CLOSED_PL);
  }


  /**
   * @copydoc Post::lock()
   */
  public function lock() {
    if (!$this->user->has(new Permission\ProtectPermission($this)))
      throw new Exception\AccessDeniedException("Privilegi insufficienti o stato incompatibile.");

    $this->protect(self::LOCKED_PL);
  }


  /**
   * @copydoc Post::removeProtection()
   */
  public function removeProtection() {
    if (!$this->user->has(new Permission\UnprotectPermission($this)))
      throw new Exception\AccessDeniedException("Privilegi insufficienti o stato incompatibile.");

    $this->unprotect();
  }


  //! @cond HIDDEN_SYMBOLS

  public function getUrl() {
    return $this->meta['url'];
  }


  public function issetUrl() {
    return isset($this->meta['url']);
  }


  public function setUrl($value) {
    $this->meta['url'] = $value;
  }


  public function unsetUrl() {
    if ($this->isMetadataPresent('url'))
      unset($this->meta['url']);
  }

  //! @endcond

}
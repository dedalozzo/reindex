<?php

/**
 * @file Update.php
 * @brief This file contains the Update class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Doc;


use ReIndex\Security\Permission\Revision\Post\Update as Permission;

use Daikengo\Exception\AccessDeniedException;


/*
 * @brief This class represents an user's update.
 * @nosubgrouping
 */
final class Update extends Post {


  /**
   * @copydoc Post::close()
   */
  public function close() {
    if (!$this->user->has(new Permission\ProtectPermission($this)))
      throw new AccessDeniedException("Insufficient privileges or illegal state.");

    $this->protect(self::CLOSED_PL);
  }


  /**
   * @copydoc Post::lock()
   */
  public function lock() {
    if (!$this->user->has(new Permission\ProtectPermission($this)))
      throw new AccessDeniedException("Insufficient privileges or illegal state.");

    $this->protect(self::LOCKED_PL);
  }


  /**
   * @copydoc Post::removeProtection()
   */
  public function removeProtection() {
    if (!$this->user->has(new Permission\UnprotectPermission($this)))
      throw new AccessDeniedException("Insufficient privileges or illegal state.");

    $this->unprotect();
  }


  //! @cond HIDDEN_SYMBOLS

  public function setTitle($value) {
    throw new \BadMethodCallException('Method is not implemented.');
  }


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
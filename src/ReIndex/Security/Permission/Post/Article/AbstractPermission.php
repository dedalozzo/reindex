<?php

/**
 * @file Article/AbstractPermission.php
 * @brief This file contains the AbstractPermission class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Security\Permission\Post\Article;


use ReIndex\Security\Permission\AbstractPermission as Superclass;
use ReIndex\Doc\Article;


abstract class AbstractPermission extends Superclass {

  protected $article;


  /**
   * @brief Constructor.
   * @param[in] Doc::Article $article
   */
  public function __construct(Article $article) {
    $this->article = $article;
    parent::__construct();
  }

}
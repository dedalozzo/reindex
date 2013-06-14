<?php

//! @file DeleteTask.php
//! @brief
//! @details
//! @author Filippo F. Fadda


//! @brief
namespace PitPress\Task;


use Phalcon\CLI\Task;


//! @brief
//! @nosubgrouping
class DeleteTask extends Task {

  // Default action.
  public function mainAction() {
    echo "Main action ...";
  }


  public function databaseAction() {

  }


  public function createAction() {
    $map = "function(\$doc) use (\$emit) {
              if (preg_match('/Display\\z/i', \$doc->docClass) === TRUE)
              \$emit(\$doc->id, NULL);
            };";

    $handler = new ViewHandler("count");
    $handler->mapFn = $map;
    $handler->useBuiltInReduceFnCount();
    $this->addHandler($handler);

  }

}
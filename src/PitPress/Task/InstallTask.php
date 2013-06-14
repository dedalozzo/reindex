<?php

//! @file InstallTask.php
//! @brief
//! @details
//! @author Filippo F. Fadda


//! @brief
namespace PitPress\Task;


use Phalcon\CLI\Task;
use ElephantOnCouch\Doc\DesignDoc;
use ElephantOnCouch\Handler\ViewHandler;

//! @brief
//! @nosubgrouping
class InstallTask extends Task {


  // Default action.
  public function mainAction() {
    echo "Main action ...";
  }


  public function allAction() {
    echo "Main action ...";
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
<?php

//! @file TestCommand.php
//! @brief This file contains the TestCommand class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use ElephantOnCouch\Opt\ViewQueryOpts;


//! @brief Creates a temp view, with a simple map function, then query it and output the result.
//! @nosubgrouping
class TestCommand extends AbstractCommand {


  //! @brief Configures the command.
  protected function configure() {
    $this->setName("test");
    $this->setDescription("Creates a temp view, with a simple map function, then query it and output the result.");
  }


  //! @brief Executes the command.
  protected function execute(InputInterface $input, OutputInterface $output) {
    $couch = $this->_di['couchdb'];

    $map = "function(\$doc) use (\$emit) {
              \$types = [
                'book' => NULL
              ];

              if (array_key_exists(\$doc->type, \$types))
                \$emit(\$doc->_id, \$doc->isbn);
            };";

    $opts = new ViewQueryOpts();
    //$opts->setLimit(2);
    $opts->includeDocs();

    echo $couch->queryTempView($map, "", NULL, $opts);
  }

}
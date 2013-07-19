<?php

//! @file QueryCommand.php
//! @brief This file contains the QueryCommand class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use ElephantOnCouch\Opt\ViewQueryOpts;


//! @brief Query a view and outputs the result.
//! @nosubgrouping
class QueryCommand extends AbstractCommand {


  //! @brief Configures the command.
  protected function configure() {
    $this->setName("query");
    $this->setDescription("Query a view and outputs the result.");

    $this->addArgument("design-doc/view-name",
      InputArgument::REQUIRED,
      "The design document name followed by the view you want query. In case of a temporary view,
      use: _temp_view --map=map.txt --reduce=reduce.txt. The files map.txt and reduce.txt must contains the map and
      reduce functions implementation.");

    $this->addArgument("keys",
      InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
      "Used to retrieve just the view rows matching that set of keys. Rows are returned in the order of the specified
      keys. Combining this feature with --include-docs results in the so-called multi-document-fetch feature.");

    $this->addOption("map",
      NULL,
      InputOption::VALUE_OPTIONAL,
      "Load map function from this file. To be used with _temp_view only, ignored otherwise.");

    $this->addOption("reduce",
      NULL,
      InputOption::VALUE_OPTIONAL,
      "Load reduce function from this file. To be used with _temp_view only, ignored otherwise.");

    $this->addOption("language",
      NULL,
      InputOption::VALUE_OPTIONAL,
      "The language used to implement the map and reduce functions. If no specified, PHP assumed");

    $this->addOption("limit",
      NULL,
      InputOption::VALUE_OPTIONAL,
      "Limit the number of results.");

    $this->addOption("include-docs",
      NULL,
      InputOption::VALUE_OPTIONAL,
      "Includes documents in the output.");

    $this->addOption("do-not-reduce",
      NULL,
      InputOption::VALUE_OPTIONAL,
      "Even is a reduce function is defined for the view, doesn't call it.");
  }


  //! @brief Executes the command.
  protected function execute(InputInterface $input, OutputInterface $output) {
    $couch = $this->_di['couchdb'];

    $view = $input->getArgument('design-doc/view-name');
    $keys = $input->getArgument('keys');
    
    // Sets the options.
    $opts = new ViewQueryOpts();   

    // Limit.
    $limit = (int)$input->getOption('limit');
    if ($limit > 0)
      $opts->setLimit($limit);

    // Map and reduce functions.
    if ($fileName = $input->getOption('map')) {
      $map = file_get_contents($fileName);

      if ($fileName = $input->getOption('reduce'))
        $reduce = file_get_contents($fileName);
      else
        $reduce = "";

      $language = $input->getOption('language');
      if (empty($language))
        $language = "php";
    }

    // Includes docs.
    if ((bool)$input->getOption('include-docs'))
      $opts->includeDocs();

    // Do not reduce.
    if ((bool)$input->getOption('do-not-reduce'))
      $opts->doNotReduce();

    if ($view == "_temp_view") {
      echo $couch->queryTempView($map, $reduce, $keys, $opts, $language);
    }
    else {
      $names = implode('/', $view);

      if (count($names) == 2)
        echo $couch->queryView($names[0], $names[1], $keys, $opts);
      else
        throw new \InvalidArgumentException("You have to specify design-doc/view-name");
    }

  }

}
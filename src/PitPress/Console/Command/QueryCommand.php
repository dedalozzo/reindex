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
//! todo Fix a bug when read map and reduce from file.
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


    // General options.
    $this->addOption("key",
      NULL,
      InputOption::VALUE_REQUIRED,
      "Returns only documents that match the specified key.");

    $this->addOption("startkey",
      NULL,
      InputOption::VALUE_REQUIRED,
      "Defines the first key to be included in the range.");

    $this->addOption("endkey",
      NULL,
      InputOption::VALUE_REQUIRED,
      "Defines the last key to be included in the range.");

    $this->addOption("startkey-docid",
      NULL,
      InputOption::VALUE_REQUIRED,
      "Sets the ID of the document with which to start the range.");

    $this->addOption("endkey-docid",
      NULL,
      InputOption::VALUE_REQUIRED,
      "Sets the ID of the document with which to end the range.");

    $this->addOption("limit",
      NULL,
      InputOption::VALUE_REQUIRED,
      "Limit the number of results.");

    $this->addOption("group-results",
      NULL,
      InputOption::VALUE_NONE,
      "Results should be grouped.");

    $this->addOption("group-level",
      NULL,
      InputOption::VALUE_REQUIRED,
      "Level at which documents should be grouped.");

    $this->addOption("do-not-reduce",
      NULL,
      InputOption::VALUE_NONE,
      "Even is a reduce function is defined for the view, doesn't call it.");

    $this->addOption("include-docs",
      NULL,
      InputOption::VALUE_NONE,
      "Includes documents in the output.");

    $this->addOption("exclude-results",
      NULL,
      InputOption::VALUE_NONE,
      "Don't get any data, but all meta-data for this View. The number of documents in this View for example.");

    $this->addOption("exclude-endkey",
      NULL,
      InputOption::VALUE_NONE,
      "Tells CouchDB to not include end key in the result.");

    $this->addOption("reverse-order",
      NULL,
      InputOption::VALUE_NONE,
      "Reverses order of results.");

    $this->addOption("skip",
      NULL,
      InputOption::VALUE_REQUIRED,
      "Skips the defined number of documents.");

    $this->addOption("include-conflicts",
      NULL,
      InputOption::VALUE_NONE,
      "Includes conflict documents.");

    $this->addOption("include-missing-keys",
      NULL,
      InputOption::VALUE_NONE,
      "Includes all the rows, even if a match for a key is not found..");


    // Temporary view options.
    $this->addOption("map",
      NULL,
      InputOption::VALUE_REQUIRED,
      "Load map function from this file. To be used with _temp_view only, ignored otherwise.");

    $this->addOption("reduce",
      NULL,
      InputOption::VALUE_REQUIRED,
      "Load reduce function from this file. To be used with _temp_view only, ignored otherwise.");

    $this->addOption("language",
      NULL,
      InputOption::VALUE_REQUIRED,
      "The language used to implement the map and reduce functions. If no specified, PHP assumed");
  }


  //! @brief Executes the command.
  protected function execute(InputInterface $input, OutputInterface $output) {
    $couch = $this->_di['couchdb'];

    $view = $input->getArgument('design-doc/view-name');

    if ($input->getArgument('keys')) {
      $args = $input->getArgument('keys');

      $keys = [];
      foreach ($args as $key)
        $keys[] = $this->castArg($key);
    }
    else
      $keys = NULL;

    // Sets the options.
    $opts = new ViewQueryOpts();

    // Key.
    if ($key = $input->getOption('key'))
      $opts->setKey($this->castArg($key));

    // Limit.
    $limit = (int)$input->getOption('limit');
    if ($limit > 0)
      $opts->setLimit($limit);

    // Group results.
    if ($input->getOption('group-results'))
      $opts->groupResults();

    // Sets group level.
    $level = (int)$input->getOption('group-level');
    if ($level > 0)
      $opts->setGroupLevel($level);

    // Do not reduce.
    if ($input->getOption('do-not-reduce'))
      $opts->doNotReduce();

    // Includes docs.
    if ($input->getOption('include-docs'))
      $opts->includeDocs();

    // Excludes results.
    if ($input->getOption('exclude-results'))
      $opts->excludeResults();

    // Excludes endkey.
    if ($input->getOption('exclude-endkey'))
      $opts->excludeEndKey();

    // Reverses order of results.
    if ($input->getOption('reverse-order'))
      $opts->reverseOrderOfResults();

    // Skips the defined number of documents.
    $skip = (int)$input->getOption('skip');
    if ($skip > 0)
      $opts->skipDocs($skip);

    // Includes conflicts.
    if ($input->getOption('include-conflicts'))
      $opts->includeConflicts();

    // Includes missing keys.
    if ($input->getOption('include-missing-keys'))
      $opts->includeMissingKeys();


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

    if ($view == "_temp_view") {
      print_r($couch->queryTempView($map, $reduce, $keys, $opts, $language));
    }
    elseif ($view == "_all_docs") {
      print_r($couch->queryAllDocs($keys, $opts));
    }
    else {
      $names = explode('/', $view, 2);

      if (count($names) == 2)
        print_r($couch->queryView($names[0], $names[1], $keys, $opts));
      else
        throw new \InvalidArgumentException("You have to specify design-doc/view-name.");
    }

  }

}
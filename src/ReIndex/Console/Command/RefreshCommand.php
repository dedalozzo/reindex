<?php

/*
 * @file RefreshCommand.php
 * @brief This file contains the RefreshCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command;


use EoC\Opt\ViewQueryOpts;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use EoC\Couch;


/**
 * @brief Refreshes the ReIndex databases' views (or a single view).
 * @nosubgrouping
 */
final class RefreshCommand extends AbstractCommand {

  /**
   * @var Couch $couch
   */
  protected $couch;

  protected $prefix;

  protected $init;


  /**
   * @brief Refresh all the views contained inside a design document.
   * @param[in] string $dbName The database's name.
   * @param[in] string $docName The design document's name.
   */
  protected function refreshViewsInDDoc($dbName, $docName) {
    $viewName = key($this->init[$dbName][$docName]) ;
    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->setLimit(0);
    $this->couch->queryView($dbName, $docName, $viewName);
  }


  /**
   * @brief Refresh all the views contained inside a database.
   * @param[in] OutputInterface $output The Symfony console output interface.
   * @param[in] string $dbName The database's name.
   * @param[in] array $ddocs An array of design documents.
   */
  protected function refreshViewsInDb(OutputInterface $output, $dbName, &$ddocs = NULL) {
    if (is_null($ddocs))
      $ddocs = &$this->init[$dbName];

    foreach ($ddocs as $docName => $views) {

      $cmd = sprintf('rei refresh %s %s', $dbName, $docName);
      $process = new Process($cmd);

      $process->start();
      $output->writeln('Please use `couch status` to see the status progression.');
      $msg = sprintf('%s %s ...refreshing', $this->prefix.$dbName, $docName);
      $output->writeln($msg);

      if (!$process->isSuccessful()) {
        throw new ProcessFailedException($process);
      }
    }
  }


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("refresh");
    $this->setDescription("Refreshes (in parallel) all the views, just the ones contained in a database or in a single design document");

    $this->addArgument("database-name",
      InputArgument::OPTIONAL,
      "An optional database name"
    );

    $this->addArgument("ddoc-name",
      InputArgument::OPTIONAL,
      "An optional design document name"
    );
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->couch = $this->di['couchdb'];
    $this->prefix = $this->couch->getDbPrefix();
    $this->init = $this->di['init'];

    if ($dbName = $input->getArgument('database-name')) {
      if (!array_key_exists($dbName, $this->init)) {
        echo "Invalid database's name." . PHP_EOL;
        exit(0);
      }

      if ($docName = $input->getArgument('ddoc-name')) {
        if (!array_key_exists($docName, $this->init[$dbName])) {
          echo "Invalid design document's name." . PHP_EOL;
          exit(0);
        }

        $this->refreshViewsInDDoc($dbName, $docName);
      }
      else
        $this->refreshViewsInDb($output, $dbName);
    }
    else {
      foreach ($this->init as $dbName => $ddocs)
        $this->refreshViewsInDb($output, $dbName, $ddocs);
    }
  }

}
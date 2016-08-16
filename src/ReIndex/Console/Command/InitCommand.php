<?php

/*
 * @file InitCommand.php
 * @brief This file contains the InitCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use EoC\Couch;
use EoC\Doc\DesignDoc;
use EoC\Handler\ViewHandler;
use EoC\Exception\ServerErrorException;


/**
 * @brief Initializes the ReIndex database, adding the required design documents.
 * @nosubgrouping
 */
final class InitCommand extends AbstractCommand {

  /**
   * @var Couch $couch
   */
  protected $couch;

  protected $prefix;
  protected $init; // The init array.


  /**
   * @brief Prints a list of all the available design documents.
   * @param[in] string $dbName The database's name.
   * @param[in] array $ddocs An array of design documents.
   */
  protected function listDDocs($dbName, &$ddocs = NULL) {
    if (is_null($ddocs))
      $ddocs = $this->init[$dbName];

    foreach ($ddocs as $docName => $views) {
      printf('%s%s/%s'.PHP_EOL, $this->prefix, $dbName, $docName);
    }
  }


  /**
   * @brief Insert a single design document within all its views.
   * @param[in] string $dbName The database's name.
   * @param[in] string $docName The design document's name.
   * @param[in] array $views An array of views.
   */
  protected function initDDoc($dbName, $docName, &$views = NULL) {
    try {
      $doc = $this->couch->getDoc($dbName, Couch::DESIGN_DOC_PATH, $docName);
    }
    catch (ServerErrorException $e) {
      if ($e->getResponse()->getStatusCode() == 404)
        $doc = DesignDoc::create($docName);
      else
        throw $e;
    }

    $doc->resetHandlers();

    if (is_null($views))
      $views = &$this->init[$dbName][$docName];

    foreach ($views as $viewName => $funcs) {
      $handler = new ViewHandler($viewName);

      $handler->mapFn = $funcs['map'];

      if (isset($funcs['reduce'])) {
        $reduce = $funcs['reduce'];

        if ($reduce == '_count')
          $handler->useBuiltInReduceFnCount();
        elseif ($reduce == '_sum')
          $handler->useBuiltInReduceFnSum();
        elseif ($reduce == '_stats')
          $handler->useBuiltInReduceFnStats();
        else
          $handler->reduceFn = $reduce;
      }

      $doc->addHandler($handler);
    }

    $this->couch->saveDoc($dbName, $doc);

    printf('%s%s/%s... done'.PHP_EOL, $this->prefix, $dbName, $docName);
  }


  /**
   * @brief Insert all design documents.
   * @param[in] string $dbName The database's name.
   * @param[in] array $ddocs An array of design documents.
   */
  protected function initDb($dbName, &$ddocs = NULL) {
    if (is_null($ddocs))
      $ddocs = &$this->init[$dbName];

    foreach ($ddocs as $docName => $views) {
      $this->initDDoc($dbName, $docName, $views);
    }
  }


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("init");
    $this->setDescription("Initializes the ReIndex platform, adding the required design documents");

    $this->addArgument("database-name",
      InputArgument::OPTIONAL,
      "An optional database name"
    );

    $this->addArgument("ddoc-name",
      InputArgument::OPTIONAL,
      "An optional design document name"
    );

    $this->addOption("list",
      "l",
      InputOption::VALUE_NONE,
      "Returns the list of available design documents eventually filtered by database");
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->couch = $this->di['couchdb'];
    $this->prefix = $this->couch->getDbPrefix();
    $this->init = $this->di['init'];

    $question = new ConfirmationQuestion('Are you sure you want init the ReIndex database [Y/n]', FALSE);

    $helper = $this->getHelper('question');

    if ($helper->ask($input, $output, $question)) {
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

          $this->initDDoc($dbName, $docName);
        } else {
          if ($input->getOption('list'))
            $this->listDDocs($dbName);
          else
            $this->initDb($dbName);
        }
      } elseif ($input->getOption('list'))
        foreach ($this->init as $dbName => $ddocs)
          $this->listDDocs($dbName, $ddocs);
      else {
        foreach ($this->init as $dbName => $ddocs)
          $this->initDb($dbName, $ddocs);
      }
    }
  }
}
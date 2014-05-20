<?php

//! @file RemoveCommand.php
//! @brief This file contains the RemoveCommand class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;


//! @brief Removes from PitPress database all documents of one or more types.
//! @nosubgrouping
class RemoveCommand extends AbstractCommand {
  private $couch;
  private $redis;

  private $input;
  private $output;


  //! @brief Remove all documents of the specified type.
  private function removeDocs($type) {
    $this->output->writeln("Removing documents of type `".$type."`...");

    $opts = new ViewQueryOpts();
    $opts->setKey($type)->includeDocs();
    $rows = $this->couch->queryView("docs", "byType", NULL, $opts)['rows'];

    $progress = $this->getApplication()->getHelperSet()->get('progress');
    $progress->start($this->output, count($rows));

    foreach ($rows as $row) {
      $this->couch->deleteDoc(Couch::STD_DOC_PATH, $row['id'], $row['doc']['_rev']);

      $progress->advance();
    }

    $this->couch->ensureFullCommit();

    $progress->finish();
  }


  //! @brief Removes all documents.
  private function removeAll() {
    $this->output->writeln("Removing all documents...");

    $opts = new ViewQueryOpts();
    $opts->includeDocs();
    $rows = $this->couch->queryAllDocs(NULL, $opts)['rows'];

    $progress = $this->getApplication()->getHelperSet()->get('progress');
    $progress->start($this->output, count($rows));

    foreach ($rows as $row) {
      $this->couch->deleteDoc(Couch::STD_DOC_PATH, $row['id'], $row['value']['rev']);

      $progress->advance();
    }

    $this->couch->ensureFullCommit();

    $progress->finish();
  }


  //! @brief Configures the command.
  protected function configure() {
    $this->setName("remove");
    $this->setDescription("Removes from PitPress database all documents of one or more types.");
    $this->addArgument("types",
        InputArgument::IS_ARRAY | InputArgument::REQUIRED,
        "The types of document you want remove. Use 'all' if you want remove all the documents, 'user' if you want
        just remove the users or separate multiple types with a space.");
  }


  //! @brief Executes the command.
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->mysql = $this->di['mysql'];
    $this->couch = $this->di['couchdb'];

    $this->input = $input;
    $this->output = $output;

    $types = $input->getArgument('types');

    // Checks if the argument 'all' is provided.
    $index = array_search("all", $types);

    if ($index === FALSE) {

      foreach ($types as $type)
        $this->removeDocs($type);

    }
    else
      $this->removeAll();
  }

}
<?php

//! @file GenerateCommand.php
//! @brief This file contains the GenerateCommand class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


//! @brief Generates fake documents for testing purpose.
//! @nosubgrouping
class GenerateCommand extends AbstractCommand {

  protected $mysql;
  protected $couch;


  //! @brief Insert all design documents.
  private function generateAll() {
    $this->generateVotes();
    $this->generateStars();
  }


  private function generateVotes() {
  }


  private function generateStars() {
  }


  //! @brief Configures the command.
  protected function configure() {
    $this->setName("generate");
    $this->setDescription("Generates fake documents for testing purpose.");
    $this->addArgument("types",
      InputArgument::IS_ARRAY | InputArgument::REQUIRED,
      "The types you want create. Use 'all' if you want generate fakes of all types, 'votes' if
      you want just generate fake votes or separate multiple types with a space. The available types are: votes, stars.");
  }


  //! @brief Executes the command.
  protected function execute(InputInterface $input, OutputInterface $output) {

    $this->mysql = $this->_di['mysql'];
    $this->couch = $this->_di['couchdb'];

    $types = $input->getArgument('types');

    // Checks if the argument 'all' is provided.
    $index = array_search("all", $types);

    if ($index === FALSE) {

      foreach ($types as $name)
        switch ($name) {
          case 'votes':
            $this->generateVotes();
            break;

          case 'stars':
            $this->generateStars();
            break;
        }

    }
    else
      $this->generateAll();
  }

}
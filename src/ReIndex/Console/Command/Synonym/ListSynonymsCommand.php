<?php

/**
 * @file ListSynonymsCommand.php
 * @brief This file contains the ListSynonymsCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command\Synonym;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use ReIndex\Console\Command\AbstractCommand;
use ReIndex\Thesaurus;


/**
 * @brief Displays all the synonyms or the ones related to a specific tag.
 * @nosubgrouping
 */
class ListSynonymsCommand extends AbstractCommand {


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("ls");
    $this->setDescription("List all the synonyms, or the ones related to a specific tag");

    $this->addArgument("name",
      InputArgument::OPTIONAL,
      "A tag's name.");
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    if ($name = $input->getArgument('name')) {
      $thesaurus = new Thesaurus();
      print_r($thesaurus->listSynonyms($name));
    }

    parent::execute($input, $output);
  }

}
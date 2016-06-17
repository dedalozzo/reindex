<?php

/**
 * @file DelSynonymCommand.php
 * @brief This file contains the DelSynonymCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command\Synonym;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use ReIndex\Console\Command\AbstractCommand;
use ReIndex\Thesaurus;


/**
 * @brief Displays the synonyms of a tag or add a tag as a synonym of another one.
 * @nosubgrouping
 */
class DelSynonymCommand extends AbstractCommand {


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("delsynonym");
    $this->setDescription("Deletes a synonym");

    $this->addArgument("synonym",
      InputArgument::REQUIRED,
      "A synonym");

    $this->addOption("force",
      'f',
      InputOption::VALUE_NONE,
      "Attempts to add the synonym without prompting for confirmation");
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $synonym = $input->getArgument('synonym');

    if (!$input->getOption('force')) {
      $question = new ConfirmationQuestion('Are you sure you want delete the synonym? [Y/n]', FALSE);
      $helper = $this->getHelper('question');
      $proceed = $helper->ask($input, $output, $question);
    }
    else
      $proceed = TRUE;

    if ($proceed) {
      $thesaurus = new Thesaurus();
      $thesaurus->delSynonym($synonym);
    }
  }

}
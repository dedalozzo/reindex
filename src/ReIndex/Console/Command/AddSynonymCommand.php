<?php

/**
 * @file AddSynonymCommand.php
 * @brief This file contains the AddSynonymCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use ReIndex\Thesaurus;

use Monolog\Logger;


/**
 * @brief Displays the synonyms of a tag or add a tag as a synonym of another one.
 * @details This class implement the IChunkHook interface.
 * @nosubgrouping
 */
class AddSynonymCommand extends AbstractCommand {

  /**
   * @var Logger $log
   */
  protected $log;


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("addsynonym");
    $this->setDescription("Displays the synonyms of a tag or add a tag as a synonym of another one or remove it.");

    $this->addArgument("tag",
      InputArgument::REQUIRED,
      "The tag's name.");

    $this->addArgument("synonym",
      InputArgument::REQUIRED,
      "A synonym.");

    $this->addOption("force",
      'f',
      InputOption::VALUE_NONE,
      "Attempts to add the synonym without prompting for confirmation.");
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $tagName = $input->getArgument('tag');
    $synonymName = $input->getArgument('synonym');

    if (!$input->getOption('force')) {
      $question = new ConfirmationQuestion('Are you sure you want add the synonym? [Y/n]', FALSE);
      $helper = $this->getHelper('question');
      $proceed = $helper->ask($input, $output, $question);
    }
    else
      $proceed = TRUE;

    if ($proceed) {
      $thesaurus = new Thesaurus();
      $thesaurus->addSynonym($tagName, $synonymName);
    }

    parent::execute($input, $output);
  }

}
<?php

/**
 * @file SynonymizeCommand.php
 * @brief This file contains the SynonymizeCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command;

use ReIndex\Model\Tag;
use ReIndex\Model\Synonym;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;

use Monolog\Logger;


/**
 * @brief Displays the synonyms of a tag or add a tag as a synonym of another one.
 * @details This class implement the IChunkHook interface.
 * @nosubgrouping
 */
class SynonymizeCommand extends AbstractCommand {

  /**
   * @var Couch $couch
   */
  protected $couch;

  /**
   * @var Logger $log
   */
  protected $log;


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("refresh");
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

    $opts = new ViewQueryOpts();
    $opts->setLimit(1);
    $opts->setKey($tagName);
    $rows = $this->couch->queryView("tags", "byName", NULL, $opts);

    if ($rows->isEmpty())
      throw new \RuntimeException('Tag not found.');

    $master = Tag::find(current($rows->getIterator())['id']);

    // Adds a synonym.
    if ($name = $input->getOption('add')) {

      if (!$input->getOption('force')) {
        $question = new ConfirmationQuestion('Are you sure you want add the synonym? [Y/n]', FALSE);
        $helper = $this->getHelper('question');
        $proceed = $helper->ask($input, $output, $question);
      }
      else
        $proceed = TRUE;

      if ($proceed) {
        $opts = new ViewQueryOpts();
        $opts->setLimit(1);
        $opts->setKey($name);
        $rows = $this->couch->queryView("tags", "andSynonymsByName", NULL, $opts);

        if ($rows->isEmpty()) {
          $synonym = Synonym::create();
          $synonym->name = $name;
          $synonym->save();
          $master->synonyms->add($synonym);
          $master->save();
        } else {
          $tag = Tag::find(current($rows->getIterator())['id']);

          if ($tag instanceof Tag)
            $tag->markAsSynonymOf($tag);
          else
            throw new \RuntimeException('The synonym already exists');
        }
      }

    }

    parent::execute($input, $output);
  }

}
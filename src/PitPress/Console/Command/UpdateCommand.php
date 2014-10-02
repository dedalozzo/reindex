<?php

/**
 * @file UpdateCommand.php
 * @brief This file contains the UpdateCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;


/**
 * @brief Updates Redis data.
 * @nosubgrouping
 */
class UpdateCommand extends AbstractCommand {

  private $couch;


  /**
   * @brief Update posts score.
   */
  private function updatePopularity(InputInterface $input, OutputInterface $output) {
    $output->writeln("Updating popularity...");

    $progress = $this->getApplication()->getHelperSet()->get('progress');

    $opts = new ViewQueryOpts();
    $opts->doNotReduce();
    $ids = array_column($this->couch->queryView('posts', 'unversion', NULL, $opts)->asArray(), 'id');

    $progress->start($output, count($ids));

    foreach ($ids as $id) {
      $post = $this->couch->getDoc(Couch::STD_DOC_PATH, $id);
      $post->updatePopularity();

      $progress->advance();
    }

    $progress->finish();
  }


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("update");
    $this->setDescription("Updates Redis database.");
    $this->addArgument("subcommand",
      InputArgument::REQUIRED,
      "The data you want update. Use 'popularity' if you want update posts popularity.");
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->input = $input;
    $this->output = $output;

    $this->couch = $this->di['couchdb'];

    $subcommand = $input->getArgument('subcommand');

    switch ($subcommand) {
      case 'popularity':
        $this->updatePopularity($input, $output);
        break;
    }

    parent::execute($input, $output);
  }

}
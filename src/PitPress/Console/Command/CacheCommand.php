<?php

/**
 * @file CacheCommand.php
 * @brief This file contains the CacheCommand class.
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
class CacheCommand extends AbstractCommand {

  private $couch;


  /**
   * @brief Updates posts popularity.
   */
  private function updatePostsPopularity(InputInterface $input, OutputInterface $output) {
    $output->writeln("Updating posts popularity...");

    $progress = $this->getApplication()->getHelperSet()->get('progress');

    $opts = new ViewQueryOpts();
    $opts->doNotReduce();
    $ids = array_column($this->couch->queryView('posts', 'unversion', NULL, $opts)->asArray(), 'id');

    $progress->start($output, count($ids));

    foreach ($ids as $id) {
      $post = $this->couch->getDoc(Couch::STD_DOC_PATH, $id);
      $post->zAddPopularity();

      $progress->advance();
    }

    $progress->finish();
  }


  /**
   * @brief Refreshes posts timestamp.
   */
  private function refreshPostsTimestamp(InputInterface $input, OutputInterface $output) {
    $output->writeln("Refreshing posts timestamp...");

    $progress = $this->getApplication()->getHelperSet()->get('progress');

    $opts = new ViewQueryOpts();
    $opts->doNotReduce();
    $ids = array_column($this->couch->queryView('posts', 'unversion', NULL, $opts)->asArray(), 'id');

    $progress->start($output, count($ids));

    foreach ($ids as $id) {
      $post = $this->couch->getDoc(Couch::STD_DOC_PATH, $id);
      $post->updatePostsPopularity();

      $progress->advance();
    }

    $progress->finish();
  }


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("cache");
    $this->setDescription("Performs database cache maintenance activities.");
    $this->addArgument("subcommand",
      InputArgument::REQUIRED, <<<'DESC'
Use `clear` to clean database cache.
Use `rebuild` to rebuild database cache.
DESC
    );
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
      case 'rebuild':
        $this->addPopularity($input, $output);
        $this->addLastUpdate($input, $output);
        break;
    }

    parent::execute($input, $output);
  }

}
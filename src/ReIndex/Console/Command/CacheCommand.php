<?php

/**
 * @file CacheCommand.php
 * @brief This file contains the CacheCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Helper\ProgressBar;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;


/**
 * @brief Updates Redis data.
 * @nosubgrouping
 */
class CacheCommand extends AbstractCommand {

  private $redis;
  private $couch;


  /**
   * @brief Clears the database cache.
   */
  private function clearCache() {
    $this->redis->flushDB();
  }


  /**
   * @brief Updates the database cache.
   */
  private function buildCache(InputInterface $input, OutputInterface $output) {
    $output->writeln("Building cache...");

    $opts = new ViewQueryOpts();
    $opts->doNotReduce();
    $ids = array_column($this->couch->queryView('posts', 'unversion', NULL, $opts)->asArray(), 'id');

    $progress = new ProgressBar($this->output, count($ids));
    $progress->setRedrawFrequency(1);
    $progress->setOverwrite(TRUE);
    $progress->start();

    foreach ($ids as $id) {
      $post = $this->couch->getDoc(Couch::STD_DOC_PATH, $id);
      $post->index();

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
      InputArgument::REQUIRED, 'Use `clear` to clean database cache. Use `build` to rebuild database cache.');
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->input = $input;
    $this->output = $output;

    $this->redis = $this->di['redis'];
    $this->couch = $this->di['couchdb'];

    $subcommand = $input->getArgument('subcommand');

    switch ($subcommand) {
      case 'clear':
        $question = new ConfirmationQuestion('Are you sure you want clear database cache? [Y/n]', FALSE);

        $helper = $this->getHelper('question');

        if ($helper->ask($input, $output, $question))
          $this->clearCache();

        break;
      case 'build':
        $question = new ConfirmationQuestion('Are you sure you want build database cache? [Y/n]', FALSE);

        $helper = $this->getHelper('question');

        if ($helper->ask($input, $output, $question))
          $this->buildCache($input, $output);

        break;
    }

    parent::execute($input, $output);
  }

}
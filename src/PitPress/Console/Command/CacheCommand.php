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
   * @brief Clears the database cache.
   */
  private function clearCache() {
    $this->redis->flushDB();
  }


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

    $this->mysql = $this->di['redis'];
    $this->couch = $this->di['couchdb'];

    $subcommand = $input->getArgument('subcommand');

    $dialog = $this->getHelperSet()->get('dialog');

    switch ($subcommand) {
      case 'clear':
        $confirm = $dialog->ask($output, 'Are you sure you want clear database cache? [Y/n]'.PHP_EOL, 'n');
        if ($confirm == 'Y')
          $this->clearCache();
        break;
      case 'rebuild':
        $confirm = $dialog->ask($output, 'Are you sure you want rebuild database cache? [Y/n]'.PHP_EOL, 'n');
        if ($confirm == 'Y') {
          $this->clearCache();
          $this->updatePostsPopularity($input, $output);
          $this->refreshPostsTimestamp($input, $output);
        }
        break;
    }

    parent::execute($input, $output);
  }

}
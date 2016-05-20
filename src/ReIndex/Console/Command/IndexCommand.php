<?php

/**
 * @file IndexCommand.php
 * @brief This file contains the IndexCommand class.
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
use EoC\Hook\IChunkHook;

use ReIndex\Extension\ICache;


/**
 * @brief Updates Redis data.
 * @nosubgrouping
 */
class IndexCommand extends AbstractCommand implements IChunkHook {

  private $redis;
  private $couch;
  private $progress;

  private $input;
  private $output;


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
    $opts->reduce();
    $docsCount = $this->couch->queryView("docs", "toIndex", NULL, $opts)->getReducedValue();

    $this->progress = new ProgressBar($output, $docsCount);
    $this->progress->setRedrawFrequency(1);
    $this->progress->setOverwrite(TRUE);
    $this->progress->start();

    $opts->reset();
    $opts->doNotReduce();

    $this->couch->queryView("docs", "toIndex", NULL, $opts, $this);

    $this->progress->finish();
  }


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("index");
    $this->setDescription("Performs database indexes maintenance activities.");
    $this->addArgument("subcommand",
      InputArgument::REQUIRED, 'Use `clear` to clean database indexes. Use `all` to recreate the indexes.');
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->input = $input;
    $this->output = $output;

    $this->config = $this->di['config'];
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
        //$question = new ConfirmationQuestion('Are you sure you want build database cache? [Y/n]', FALSE);

        //$helper = $this->getHelper('question');

        //if ($helper->ask($input, $output, $question))
          $this->buildCache($input, $output);

        break;
    }

    parent::execute($input, $output);
  }


  public function process($chunk) {
    $row = json_decode($chunk);

    if (is_null($row))
      return;

    $config = $this->config;

    $cmd = 'rei cache '. $row->id;

    exec($cmd);

      $porcodio->selectDb($config->couchdb->database);

      $doc = $porcodio->getDoc(Couch::STD_DOC_PATH, $row->id);

      if ($doc instanceof ICache)
        $doc->index();


    $this->progress->advance();
  }

}
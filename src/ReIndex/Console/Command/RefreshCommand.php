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


/**
 * @brief Updates Redis data.
 * @nosubgrouping
 */
class IndexCommand extends AbstractCommand implements IChunkHook {

  private $redis;
  private $couch;
  private $rabbit;
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
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("index");
    $this->setDescription("Performs database indexes maintenance activities.");
    $this->addArgument("subcommand",
      InputArgument::REQUIRED, 'Use `clear` to clean the indexes. Use `build` to recreate the indexes.');
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->input = $input;
    $this->output = $output;

    $this->redis = $this->di['redis'];
    $this->couch = $this->di['couchdb'];
    $this->rabbit = $this->di['rabbitmq'];

    $subcommand = $input->getArgument('subcommand');

    switch ($subcommand) {
      case 'clear':
        $question = new ConfirmationQuestion('Are you sure you want clear the indexes? [Y/n]', FALSE);

        $helper = $this->getHelper('question');

        if ($helper->ask($input, $output, $question))
          $this->clearCache();

        break;
      case 'all':
        $question = new ConfirmationQuestion('Are you sure you want build database cache? [Y/n]', FALSE);

        $helper = $this->getHelper('question');

        if ($helper->ask($input, $output, $question)) {
          $output->writeln("Indexing documents...");

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

        break;
    }

    parent::execute($input, $output);
  }


  /**
   * @brief In case a chunk represent a document, creates the corespondent task and enqueue it.
   */
  public function process($chunk) {
    $row = json_decode(trim($chunk, ',\r\n'));

    if (is_null($row))
      return;

    // In order to execute a command have have it not hang your php script while it runs, the program you run must not
    // output back to php. To do this, redirect both stdout and stderr to /dev/null, then background it.
    // @see http://stackoverflow.com/a/3819422/1889828
    //$cmd = 'nohup rei index '. $row->id . '> /dev/null 2>&1 &';

    //exec($cmd);
    $doc = $this->couch->getDoc(Couch::STD_DOC_PATH, $row->id);

    $this->log->addDebug($doc->id);

    if ($doc instanceof ICache) {
      $channel = new AMQPChannel($this->rabbit);
      $exchange = new AMQPExchange($channel);

      $channel->startTransaction();

      $exchange->publish($message, 'task_queue');

      $channel->commitTransaction();

      // Adds the task to RabbitMQ.
      //$doc->index();
    }
    else
      new \RuntimeException("The document cannot be indexed.");

    $this->progress->advance();
  }

}
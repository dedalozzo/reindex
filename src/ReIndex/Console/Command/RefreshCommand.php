<?php

/**
 * @file RefreshCommand.php
 * @brief This file contains the RefreshCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command;

use ReIndex\Queue\TaskQueue;
use ReIndex\Task;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Helper\ProgressBar;

use EoC\Opt\ViewQueryOpts;
use EoC\Hook\IChunkHook;

use Monolog\Logger;


/**
 * @brief Refreshes the database cache.
 * @details This class implement the IChunkHook interface.
 * @nosubgrouping
 */
class RefreshCommand extends AbstractCommand implements IChunkHook {

  /**
   * @var TaskQueue $queue
   */
  protected $queue;

  /**
   * @var Logger $log
   */
  protected $log;

  /**
   * @var ProgressBar $progress
   */
  private $progress;


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("refresh");
    $this->setDescription("Refreshes the application cache.");
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $question = new ConfirmationQuestion('Are you sure you want refresh application cache? [Y/n]', FALSE);

    $helper = $this->getHelper('question');

    if ($helper->ask($input, $output, $question)) {
      $output->writeln("Refreshing application cache...");

      $this->queue = $this->di['taskqueue'];
      
      // We can't use this instance inside the `process()` method.
      $couch = $this->di['couchdb'];

      $opts = new ViewQueryOpts();
      $opts->reduce();
      $docsCount = $couch->queryView("tasks", "all", NULL, $opts)->getReducedValue();

      $this->progress = new ProgressBar($output, $docsCount);
      $this->progress->setRedrawFrequency(1);
      $this->progress->setOverwrite(TRUE);
      $this->progress->start();

      $opts->reset();
      $opts->doNotReduce();

      $couch->queryView("tasks", "all", NULL, $opts, $this);

      $this->progress->finish();
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

    $class = $row->class;

    $post = new $class;
    $post->id = $row->id;

    $tasks = $row->tasks;

    foreach ($tasks as $task) {
      $task = new $task['class']($post);
      $this->queue->add($task);
    }

    $this->progress->advance();
  }

}
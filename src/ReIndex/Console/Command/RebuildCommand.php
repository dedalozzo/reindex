<?php

/**
 * @file RebuildCommand.php
 * @brief This file contains the RebuildCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command;

use ReIndex\Queue\TaskQueue;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;
use EoC\Hook\IChunkHook;

use Monolog\Logger;


/**
 * @brief Refreshes the database cache.
 * @details This class implement the IChunkHook interface.
 * @nosubgrouping
 */
final class RebuildCommand extends AbstractCommand implements IChunkHook {

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
   * @var Couch $couch
   */
  protected $couch;


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("refresh");
    $this->setDescription("Rebuild the application cache");

    $this->addArgument("database-name",
      InputArgument::OPTIONAL,
      "An optional database name"
    );

    $this->addOption("id",
      NULL,
      InputOption::VALUE_REQUIRED,
      "When provided, executes the tasks associated to the related document, if any");
  }


  /**
   * @brief Enqueues the tasks.
   * @param[in] string $dbName The database's name.
   * @param[in] OutputInterface $output The Symfony console output interface.
   */
  protected function queueTasks($dbName, OutputInterface $output) {
    $opts = new ViewQueryOpts();
    $opts->reduce();
    $docsCount = $this->couch->queryView($dbName, 'tasks', 'view', NULL, $opts)->getReducedValue();

    $this->progress = new ProgressBar($output, $docsCount);
    $this->progress->setRedrawFrequency(1);
    $this->progress->setOverwrite(TRUE);
    $this->progress->start();

    $opts->reset();
    $opts->doNotReduce();

    $this->couch->queryView($dbName, 'tasks', 'view', NULL, $opts, $this);

    $this->progress->finish();
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->queue = $this->di['taskqueue'];

    // We can't use this instance inside the `process()` method.
    $this->couch = $this->di['couchdb'];
    $databases = $this->di['init'];

    if ($dbName = $input->getOption('database-name')) {
      if (!array_key_exists($dbName, $databases)) {
        $output->writeln("There is no database with such name");
        exit(0);
      }

      if ($id = $input->getOption('id')) {
        $opts = new ViewQueryOpts();
        $opts->doNotReduce()->setKey($id);
        $rows = $this->couch->queryView($dbName, 'tasks', 'view', NULL, $opts);

        if (!$rows->isEmpty()) {
          foreach ($rows as $row) {
            $docClass = $row['value']['docClass'];
            $taskClass = $row['value']['taskClass'];

            $doc = new $docClass;
            $doc->id = $row['id'];

            $task = new $taskClass($doc);
            $this->queue->add($task);
          }
        }
        else
          $output->writeln("There aren't tasks associated to the document");
      }
      else {
        $question = new ConfirmationQuestion('Are you sure you want partially rebuild application cache? [Y/n]', FALSE);

        $helper = $this->getHelper('question');

        if ($helper->ask($input, $output, $question)) {
          $output->writeln("Refreshing application cache...");

          $this->queueTasks($dbName, $output);
        }

        parent::execute($input, $output);
      }
    }
    else {
      $question = new ConfirmationQuestion('Are you sure you want rebuild application cache? [Y/n]', FALSE);

      $helper = $this->getHelper('question');

      if ($helper->ask($input, $output, $question)) {
        $output->writeln("Refreshing application cache...");

        $redis = $this->di['redis'];
        $redis->flushAll();

        foreach ($databases as $dbName => $ddocs) {
          if (!array_key_exists('tasks', $ddocs))
            continue;

          $this->queueTasks($dbName, $output);
        }

        parent::execute($input, $output);
      }
    }
  }


  /**
   * @brief Extracts from the chunk the document's ID which the task belongs, along with the task's class, creates the corespondent task and enqueue it.
   */
  public function process($chunk) {
    $row = json_decode(trim($chunk, ',\r\n'));

    if (is_null($row))
      return;

    $docClass = $row->value->docClass;
    $taskClass = $row->value->taskClass;

    $doc = new $docClass;
    $doc->id = $row->id;

    $task = new $taskClass($doc);
    $this->queue->add($task);

    $this->progress->advance();
  }

}
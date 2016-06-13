<?php

/**
 * @file SynonymizeCommand.php
 * @brief This file contains the SynonymizeCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace ReIndex\Console\Command;

use ReIndex\Queue\TaskQueue;
use ReIndex\Task;
use ReIndex\Task\IndexPostTask;
use ReIndex\Model\Update;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Helper\ProgressBar;

use EoC\Couch;
use EoC\Opt\ViewQueryOpts;
use EoC\Hook\IChunkHook;

use Monolog\Logger;


/**
 * @brief Displays the synonyms of a tag or add a tag as a synonym of another one.
 * @details This class implement the IChunkHook interface.
 * @nosubgrouping
 */
class SynonymizeCommand extends AbstractCommand implements IChunkHook {

  /**
   * @var Couch $couch
   */
  protected $couch;

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
    $this->setDescription("Displays the synonyms of a tag or add a tag as a synonym of another one.");

    $this->addArgument("tag",
      InputArgument::REQUIRED,
      "The tag's name or ID.");

    $this->addOption("add",
      NULL,
      InputOption::VALUE_REQUIRED,
      "Adds a synonym to the tag.");

    $this->addOption("remove",
      NULL,
      InputOption::VALUE_REQUIRED,
      "Removes a synonym from the tag.");

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



    // Adds a synonym.
    if ($input->getOption('add') or $input->getOption('remove')) {

      if ($input->getOption('force')) {
        $proceed = TRUE;
      } else {
        $question = new ConfirmationQuestion('Are you sure you want add the synonym? [Y/n]', FALSE);
        $helper = $this->getHelper('question');
        $proceed = $helper->ask($input, $output, $question);
      }

      if ($proceed) {
        $opts = new ViewQueryOpts();
        $opts->setLimit(1);
        $opts->setKey();
        $rows = $this->couch->queryView("tags", "byNameSpecial", NULL, $opts);

        if (!$rows->isEmpty()) {
          $tag = Tag::find(current($rows->getIterator())['id']);

          // You can't add a synonym to a synonym, neither you can add a master to a synonym.
          if (!$this->meta['master'] or !$this->meta['state'] == VersionState::CURRENT
            or $tag->isSynonym() or !$tag->state->isCurrent())
            throw new \RuntimeException("You can't add a synonym to a synonym, neither you can add a master to a synonym.");
        }
        else {
          $tag = Tag::create();
        }

        $this->progress->finish();
      }

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

    // We use update here but we could use article, question, etc. We don't really care, we just need a subclass
    // of Post, since it's abstract and we can't instantiate it.
    $task = new IndexPostTask(Update::create($row->id));

    // Enqueues the task.
    $this->queue->add($task);

    $this->progress->advance();
  }

}
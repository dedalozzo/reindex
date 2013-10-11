<?php

//! @file UpdateCommand.php
//! @brief This file contains the UpdateCommand class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use ElephantOnCouch\Opt\ViewQueryOpts;

use PitPress\Hook\UpdateScoreHook;


class UpdateCommand extends AbstractCommand {
  private $couch;


  //! @brief Updates the score of each post.
  private function updateScore() {
    $endKey = time() - 600; // Ten minutes ago.
    $hook = new UpdateScoreHook(); // The chunk hook.
    $hook->setDi($this->getDI());

    $opts = new ViewQueryOpts();
    $opts->doNotReduce()->includeDocs()->setEndKey($endKey);
    $this->couch->queryView("votes", "notRecorded", NULL, $opts, $hook);
  }


  //! @brief Updates all entities.
  private function updateAll() {
    $this->updateScore();
  }


  //! @brief Configures the command.
  protected function configure() {
    $this->setName("update");
    $this->setDescription("Updates the provided entities.");
    $this->addArgument("entities",
      InputArgument::IS_ARRAY | InputArgument::REQUIRED,
      "The entities you want update. Use 'all' if you want update all the entities, 'score' if you want just update the
      score or separate multiple entities with a space. The available entities are: score.");
    /*$this->addOption("limit",
      NULL,
      InputOption::VALUE_OPTIONAL,
      "Limit the imported records.");*/
  }


  //! @brief Executes the command.
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->couch = $this->_di['couchdb'];
    $this->redis = $this->_di['redis'];

    $this->input = $input;
    $this->output = $output;

    $entities = $input->getArgument('entities');
    //$limit = (int)$input->getOption('limit');

    /*if ($limit > 0)
      $this->limit = " LIMIT ".(string)$limit;
    else
      $this->limit = "";*/

    // Checks if the argument 'all' is provided.
    $index = array_search("all", $entities);

    if ($index === FALSE) {

      foreach ($entities as $name)
        switch ($name) {
          case 'users':
            $this->updateScore();
            break;
        }

    }
    else
      $this->updateAll();

    $this->couch->ensureFullCommit();
  }

} 
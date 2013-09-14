<?php

//! @file GenerateCommand.php
//! @brief This file contains the GenerateCommand class.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use PitPress\Model\Accessory\Vote;


//! @brief Generates fake documents for testing purpose.
//! @nosubgrouping
class GenerateCommand extends AbstractCommand {

  const ARTICLE = 2;
  const BOOK = 11;

  private $limit = 40;

  private $mysql;
  private $couch;

  private $input;
  private $output;


  //! @brief Insert all design documents.
  private function generateAll() {
    $this->generateVotes();
  }


  private function generateVotes() {
    $usersCount = mysqli_fetch_array(mysqli_query($this->mysql, "SELECT COUNT(*) FROM Member"))[0];

    $sql = "SELECT id, stereotype FROM Item WHERE (stereotype = ".self::ARTICLE." OR stereotype = ".self::BOOK.") ORDER BY date DESC";
    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    $rows = mysqli_num_rows($result);
    $progress = $this->getApplication()->getHelperSet()->get('progress');
    $progress->start($this->output, $rows);

    while ($item = mysqli_fetch_object($result)) {
      $offset = rand(0, $usersCount);
      $counter = rand(0, $this->limit);

      if ($cursor = mysqli_query($this->mysql, "SELECT id FROM Member LIMIT $counter OFFSET $offset")) {

        while ($row = mysqli_fetch_array($cursor)) {
          $userId = $row[0];
          $value = rand(0, 100) > 7 ? 1 : -1;
          $postType = ($item->stereotype == self::ARTICLE) ? 'article' : 'book';

          $vote = Vote::create($postType, 'blog', $item->id, $userId, $value);
          $this->couch->saveDoc($vote);
        }

        mysqli_free_result($cursor);
      }
      else
        continue;

      $progress->advance();
    }

    mysqli_free_result($result);

    $progress->finish();
  }


  //! @brief Configures the command.
  protected function configure() {
    $this->setName("generate");
    $this->setDescription("Generates fake accessory documents per post.");
    $this->addArgument("types",
      InputArgument::IS_ARRAY | InputArgument::REQUIRED,
      "The types you want create. Use 'all' if you want generate fakes of all types, 'votes' if
      you want just generate fake votes or separate multiple types with a space. The available types are: votes.");
    $this->addOption("limit",
      NULL,
      InputOption::VALUE_OPTIONAL,
      "Limit the number of fake documents per post.");
  }


  //! @brief Executes the command.
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->input = $input;
    $this->output = $output;

    $this->mysql = $this->_di['mysql'];
    $this->couch = $this->_di['couchdb'];

    $types = $input->getArgument('types');

    $limit = (int)$input->getOption('limit');

    if ($limit > 0)
      $this->limit = $limit;

    // Checks if the argument 'all' is provided.
    $index = array_search("all", $types);

    if ($index === FALSE) {

      foreach ($types as $name)
        switch ($name) {
          case 'votes':
            $this->generateVotes();
            break;
        }

    }
    else
      $this->generateAll();
  }

}
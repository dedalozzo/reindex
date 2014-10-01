<?php

/**
 * @file GenerateCommand.php
 * @brief This file contains the GenerateCommand class.
 * @details
 * @author Filippo F. Fadda
 */


namespace PitPress\Console\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use SimplePie;

use PitPress\Model\Accessory\Vote;
use PitPress\Model\Link;
use PitPress\Helper\Text;

use ElephantOnCouch\Couch;
use ElephantOnCouch\Opt\ViewQueryOpts;


/**
 * @brief Generates fake documents for testing purpose.
 * @nosubgrouping
 */
class GenerateCommand extends AbstractCommand {

  const ARTICLE = 2;
  const BOOK = 11;

  private $mysql;
  private $couch;


  /**
   * @brief Generates fake votes.
   */
  private function generateVotes(InputInterface $input, OutputInterface $output) {
    $output->writeln("Generating votes...");

    $limit = (int)$input->getOption('limit');

    if ($limit <= 0)
      $limit = 50;

    // Generates only positive votes.
    if ($input->getOption('only-positive'))
      $onlyPositive = TRUE;
    else
      $onlyPositive = FALSE;

    $usersCount = mysqli_fetch_array(mysqli_query($this->mysql, "SELECT COUNT(*) FROM Member"))[0];

    $sql = "SELECT id, stereotype FROM Item WHERE (stereotype = ".self::ARTICLE." OR stereotype = ".self::BOOK.") ORDER BY date DESC";
    $result = mysqli_query($this->mysql, $sql) or die(mysqli_error($this->mysql));

    $rows = mysqli_num_rows($result);
    $progress = $this->getApplication()->getHelperSet()->get('progress');
    $progress->start($output, $rows);

    while ($item = mysqli_fetch_object($result)) {
      $offset = rand(0, $usersCount);
      $counter = rand(0, $limit);

      if ($cursor = mysqli_query($this->mysql, "SELECT id FROM Member LIMIT $counter OFFSET $offset")) {

        while ($row = mysqli_fetch_array($cursor)) {
          $userId = $row[0];

          if ($onlyPositive)
            $value = 1;
          else
            $value = rand(0, 100) > 7 ? 1 : -1;

          $vote = Vote::create($item->id, $userId, $value);
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


  /*
   * @brief Consumes a site feed and generate a link for every item.
   */
  private function generateLinks(InputInterface $input, OutputInterface $output) {
    $output->writeln("Generating links...");

    $opts = new ViewQueryOpts();
    $opts->setKey('redazione')->setLimit(1);
    $result = $this->couch->queryView("users", "byUsername", NULL, $opts);

    // If the user doesn't exist, raise an exception.
    if ($result->isEmpty())
      throw new \RuntimeException('User not found.');
    else
      $userId = $result[0]['value'];

    // Consume the feed.
    $feed = new SimplePie();
    $feed->set_feed_url($input->getOption('feed'));
    $feed->init();

    $progress = $this->getApplication()->getHelperSet()->get('progress');
    $progress->start($output, $feed->get_item_quantity());

    foreach ($feed->get_items() as $item) {
      $link = Link::create();
      $link->title = $item->get_title();

      $purged = Text::purge($item->get_description());
      $link->excerpt = Text::truncate($purged);

      $link->url = $item->get_link();
      $link->userId = $userId;
      $link->createdAt = time();
      $link->publishedAt = $link->createdAt;

      $link->approve();
      $link->save();

      $progress->advance();
    }

    $progress->finish();
  }


  /**
   * @brief Configures the command.
   */
  protected function configure() {
    $this->setName("generate");
    $this->setDescription("Generates fake documents.");
    $this->addArgument("subcommand",
      InputArgument::REQUIRED,
      "The fake documents you want create. Use 'votes' if you want generate fake votes or 'links' to consume a feed and
      import links from it.");

    // This is used just in case of fake votes, ignored otherwise.
    $this->addOption("limit",
      NULL,
      InputOption::VALUE_OPTIONAL,
      "Limit the number of fake votes per post.");

    // This is used just in case of fake votes, ignored otherwise.
    $this->addOption("only-positive",
      NULL,
      InputOption::VALUE_NONE,
      "Generates only positive votes.");

    // This is used when reading from a feed, ignored otherwise.
    $this->addOption("feed",
      NULL,
      InputOption::VALUE_REQUIRED,
      "Feed URL.");
  }


  /**
   * @brief Executes the command.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->input = $input;
    $this->output = $output;

    $this->mysql = $this->di['mysql'];
    $this->couch = $this->di['couchdb'];

    $subcommand = $input->getArgument('subcommand');

    switch ($subcommand) {
      case 'votes':
        $this->generateVotes($input, $output);
        break;
      case 'links':
        $this->generateLinks($input, $output);
        break;
    }

    parent::execute($input, $output);
  }

}
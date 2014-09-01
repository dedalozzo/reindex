<?php

/**
 * @file UpdateScoreHook.php
 * @brief This file contains the UpdateScoreHook class.
 * @details
 * @author Filippo F. Fadda
 */


//! This is the hooks namespace.
namespace PitPress\Hook;

use Phalcon\CLI\Console\Exception;
use Phalcon\DI\InjectionAwareInterface;

use ElephantOnCouch\Hook\IChunkHook;
use ElephantOnCouch\Opt\ViewQueryOpts;
use ElephantOnCouch\Couch;

use PitPress\Model\Accessory\Vote;
use PitPress\Model\Accessory\Score;
use PitPress\Helper\ArrayHelper;


/**
 * @brief This class calculates the score for every single post.
 * @nosubgrouping
 */
class UpdateScoreHook implements IChunkHook, InjectionAwareInterface {

  private $di;
  private $config;
  private $logger;
  private $couch;
  private $couch2;

  private $votes = [];
  private $counter = 0;
  private $limit = 10000;

  private $firstChunkRead = FALSE;
  private $lastChunkRead = FALSE;


  /**
   * @brief Reduces the votes.
   * @return int The sum of the votes.
   */
  private function reduce() {
    $reductions = [];
    foreach ($this->votes as $vote) {

      if (isset($reductions[$vote->postId]))
        $reductions[$vote->postId] += $vote->getValue();
      else
        $reductions[$vote->postId] = $vote->getValue();

      $vote->markAsRecorded();
    }

    return $reductions;
  }


  /**
   * @brief Processes the chunk.
   */
  public function process($chunk) {
    $chunk = trim($chunk, ",\r\n");

    if (preg_match('/\A[{].+[}]\z/i', $chunk)) {
      $array = ArrayHelper::fromJson($chunk, TRUE)['doc'];

      $vote = new Vote();
      $vote->assignArray($array);
      $this->votes[] = $vote;

      $this->counter++;
    }
    else {
      if (!$this->firstChunkRead)
        $this->firstChunkRead = TRUE;
      else
        $this->lastChunkRead = TRUE;
    }

    if (($this->counter == $this->limit) or ($this->lastChunkRead && $this->counter != 0)) {
      $docs = [];

      $reductions = $this->reduce();

      $keys = array_keys($reductions);
      $partialScores = array_values($reductions);

      // Scores.
      $opts = new ViewQueryOpts();
      $opts->includeMissingKeys()->doNotReduce()->includeDocs();
      $scores = $this->couch2->queryView("scores", "perPost", $keys, $opts);

      $counter = count($keys);
      for ($i = 0; $i < $counter; $i++) {

        if (isset($scores[$i]['id'])) {
          $score = new Score();
          $score->assignArray($scores[$i]['doc']);
          $score->addPoints($partialScores[$i]);
          $docs[] = $score;
        }
        else {
          $post = $this->couch2->getDoc(Couch::STD_DOC_PATH, $keys[$i]);

          if (is_object($post)) {
            $score = Score::create($post->type, $post->id, $post->publishingDate, $partialScores[$i]);
            $docs[] = $score;
          }
          else {
            throw new Exception("Non esiste un post idenficato dall'id");
          }
        }
      }

      $this->counter = 0;

      $docs = array_merge($docs, $this->votes);
      $this->couch2->performBulkOperations($docs, FALSE, TRUE);
      unset($this->$votes);
    }

  }


  /**
   * @brief Sets the Dependency Injector.
   */
  public function setDi($di) {
    $this->di = $di;
    $this->config = $this->di['config'];
    $this->logger = $this->di['logger'];
    $this->couch = $this->di['couchdb'];

    $this->couch2 = new Couch($this->config->couchdb->ipAddress.":".$this->config->couchdb->port, $this->config->couchdb->user, $this->config->couchdb->password, FALSE);
    $this->couch2->selectDb($this->config->couchdb->database);
  }


  /**
   * @brief Gets the Dependency Injector.
   */
  public function getDi() {
    return $this->di;
  }

}
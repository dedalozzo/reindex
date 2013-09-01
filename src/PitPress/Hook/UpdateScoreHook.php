<?php

//! @file UpdateScoreHook.php
//! @brief This file contains the UpdateScoreHook class.
//! @details
//! @author Filippo F. Fadda


//! @brief This is the hooks namespace
namespace PitPress\Hook;

use Phalcon\DI\InjectionAwareInterface;

use ElephantOnCouch\Hook\ChunkHook;
use ElephantOnCouch\Helper\ArrayHelper;
use ElephantOnCouch\Opt\ViewQueryOpts;
use ElephantOnCouch\Couch;

use PitPress\Model\Accessory\Vote;
use PitPress\Model\Accessory\Score;


class UpdateScoreHook implements ChunkHook, InjectionAwareInterface {

  private $di;
  private $logger;
  private $couch;

  private $votes = [];
  private $counter = 0;
  private $limit = 25;

  private $firstChunkRead = FALSE;
  private $lastChunkRead = FALSE;


  private function reduce() {
    $reductions = [];
    foreach ($this->votes as $vote)
      $reductions[$vote->postId] += $vote->value;

    return $reductions;
  }


  public function process($chunk) {

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
      $scores = $this->couch->queryView("scores", "perPost", $keys, $opts)['rows'];

      $counter = count($keys);
      for ($i = 0; $i < $counter; $i++) {

        if (isset($scores[$i]['id'])) {
          $score = new Score();
          $array = ArrayHelper::fromJson($scores[$i], TRUE)['doc'];
          $score->assignArray($array);
          $score->addPoints($partialScores[$i]);
          $docs[] = $score;
        }
        else {
          $post = $this->couch->getDoc(Couch::STD_DOC_PATH, $keys[$i]);

          if (is_object($post)) {
            $score = Score::create($post->section, $post->type, $post->id, $post->publishingDate, $partialScores[$i]);
            $docs[] = $score;
          }
        }
      }

      $this->counter = 0;

      $docs = array_merge($docs, $this->votes);
      $this->couch->performBulkOperations($docs, FALSE, TRUE, FALSE);
    }

  }


  //! @brief Sets the Dependency Injector.
  public function setDi($di) {
    $this->di = $di;
    $this->logger = $this->di['logger'];
    $this->couch = $this->di['couch'];
  }


  //! @brief Gets the Dependency Injector.
  public function getDi() {
    return $this->di;
  }

}
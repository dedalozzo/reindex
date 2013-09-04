<?php

//! @file ForumGroup.php
//! @brief Group of Forum routes.
//! @details
//! @author Filippo F. Fadda


namespace PitPress\Route;


use Phalcon\Mvc\Router\Group;


//! @brief Group of forum routes.
//! @nosubgrouping
class ForumGroup extends Group {


  public function initialize() {
    // Sets the default controller for the following routes.
    $this->setPaths(
      [
        'namespace' => 'PitPress\Controller',
        'controller' => 'forum'
      ]);

    // All the routes start with /domande.
    $this->setPrefix('/domande');

    $this->addGet('/', ['action' => 'newest']);
    $this->addGet('/nuove/', ['action' => 'newest']);
    $this->addGet('/importanti/', ['action' => 'important']);
    $this->addGet('/popolari/', ['action' => 'weeklyPopular']);
        $this->addGet('/popolari/settimana/', ['action' => 'weeklyPopular']);
        $this->addGet('/popolari/mese/', ['action' => 'monthlyPopular']);
        $this->addGet('/popolari/trimestre/', ['action' => 'quarterlyPopular']);
        $this->addGet('/popolari/anno/', ['action' => 'yearlyPopular']);
        $this->addGet('/popolari/sempre/', ['action' => 'everPopular']);
    $this->addGet('/aggiornate/', ['action' => 'updated']);
    $this->addGet('/interessanti/', ['action' => 'interesting']);
    $this->addGet('/aperte/', ['action' => 'stillOpenForMe']);
        $this->addGet('/aperte/rivolte-a-me/', ['action' => 'stillOpenForMe']);
        $this->addGet('/aperte/nuove/', ['action' => 'stillOpenNewest']);
        $this->addGet('/aperte/popolari/', ['action' => 'stillOpenPopular']);
        $this->addGet('/aperte/nessuna-risposta/', ['action' => 'stillOpenNoAnswer']);

    $this->addGet('/rss', ['action' => 'rss']);
  }

}
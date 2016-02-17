<?php

/**
 * @file ErrorHandler.php
 * @brief This file contains the ErrorHandler class.
 * @details
 * @author Filippo F. Fadda
 */


//! Error handlers and other types of handlers
namespace ReIndex\Handler;


use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

use Phalcon\DI;
use Phalcon\Mvc\View;

use ReIndex\Version;


/**
 * @brief A monolog error handler that redirects the user to an error page.
 * @nosubgrouping
 */
class ErrorHandler extends AbstractProcessingHandler {
  protected $di; // Stores the default Dependency Injector.
  protected $user; // Stores the current user.


  /**
   * @brief Creates the error handler.
   * @param[in] int $level The minimum error level the handler should work.
   * @param[in] bool $bubble When `false` the handler stop the propagation to next handler.
   */
  public function __construct($level = Logger::ERROR, $bubble = TRUE) {
    $this->di = DI::getDefault();
    $this->dispatcher = $this->di['dispatcher'];
    $this->user = $this->di['guardian']->getUser();
    $this->assets = $this->di['assets'];

    // It is just the primary domain, for example: `reindex.xyz`.
    $this->domainName = $this->di['config']['application']['domainName'];

    // Includes the subdomain if any, for example: `it-it.reindex.xyz`.
    $this->serverName = $_SERVER['SERVER_NAME'];

    $this->themeName = $this->di['config']['application']['themeName'];
    $this->dist = "/reindex/themes/".$this->themeName."/dist";

    parent::__construct($level, $bubble);
  }


  /**
   * @brief Displays an error page.
   * @param[in] array $record A record to be logged.
   * @todo Use 500.title and 500.message to localize the error title and message.
   */
  protected function write(array $record) {
    // Cleans the output buffer or the error is shown at the end of the previously page.
    ob_clean();

    $view = $this->di['view'];

    header("HTTP/1.0 500 Internal Server Error");

    // Includes the common assets.
    // Remember to never include the assets in the `afterExecuteRoute()` method!
    $this->assets->addCss($this->dist."/css/".$this->themeName.".css", FALSE);

    $view->setVar('year', date('Y'));

    $view->setVar('version', Version::getNumber());

    $view->setVar('user', $this->user);
    $view->setVar('domainName', $this->domainName);
    $view->setVar('serverName', $this->serverName);

    $view->setVar('dist', $this->dist);

    $view->setVar('code', '500');
    $view->setVar('title', 'Errore interno del server');
    $view->setVar('message', 'Si è verificato un errore. Siamo spiacenti ma non è possibile creare la pagina richiesta.');
    $view->setVar('method', strtolower($_SERVER['REQUEST_METHOD']));
    $view->setVar('url', "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);

    $view->disableLevel(View::LEVEL_MAIN_LAYOUT);
    $view->pick('views/error/basic');
    $view->start();
    $view->render("error", "basic");
    $view->finish();

    echo $view->getContent();
  }

}
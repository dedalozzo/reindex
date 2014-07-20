<?php

/**
 * @file twig.php
 * @brief Registers Twig as a service.
 * @details
 * @author Filippo F. Fadda
 */


use Phalcon\Mvc\View\Engine\Twig;


// Creates an instance of Twig template engine and return it.
$di->setShared('twig',
  function($view, $di) use ($root, $config) {
    // The following options are available:

    // debug: When set to true, the generated templates have a __toString() method that you can use to display the
    // generated nodes (default to false).

    // charset: The charset used by the templates (default to utf-8).

    // base_template_class: The base template class to use for generated templates (default to Twig_Template).

    // cache: An absolute path where to store the compiled templates, or false to disable caching (which is the default).

    // auto_reload: When developing with Twig, it's useful to recompile the template whenever the source code changes.
    // If you don't provide a value for the auto_reload option, it will be determined automatically based on the debug
    // value.

    // strict_variables: If set to false, Twig will silently ignore invalid variables (variables and or attributes/methods
    // that do not exist) and replace them with a null value. When set to true, Twig throws an exception instead (default
    // to false).

    // autoescape: If set to true, auto-escaping will be enabled by default for all templates (default to true). As of
    // Twig 1.8, you can set the escaping strategy to use (html, js, false to disable). As of Twig 1.9, you can set the
    // escaping strategy to use (css, url, html_attr, or a PHP callback that takes the template "filename" and must return
    // the escaping strategy to use -- the callback cannot be a function name to avoid collision with built-in escaping
    // strategies).

    // optimizations: A flag that indicates which optimizations to apply (default to -1 -- all optimizations are enabled;
    // set it to 0 to disable).

    $options = [
      'cache'=> $root.$config->application->cacheDir.'twig/',
      'auto_reload' => TRUE,
      'autoescape' => FALSE
    ];

    $twig = new Twig($view, $di, $options);

    /*
    $compiler = $twig->getCompiler();

    $compiler->addFilter('minustospace',
      function($resolvedArgs, $exprArgs) {
        return "str_replace('-', ' ', ".$resolvedArgs.")";
      }
    );

    $compiler->addFunction('arraycolumn', 'array_column');
    */

    return $twig;
  }
);
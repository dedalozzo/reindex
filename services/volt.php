<?php

/**
 * @file volt.php
 * @brief Registers Volt as a service.
 * @details
 * @author Filippo F. Fadda
 */


use Phalcon\Mvc\View\Engine\Volt;


// Creates an instance of Volt template engine and return it.
$di->setShared('volt',
  function($view, $di) use ($root, $config) {
    $volt = new Volt($view, $di);

    $volt->setOptions(
      [
        'compiledPath' => $root.'/cache/volt/',
        'compiledExtension' => '.compiled',
        'compiledSeparator' => '_',
        'compileAlways' => TRUE
      ]
    );

    $compiler = $volt->getCompiler();

    // Replaces the minus character with a space.
    $compiler->addFilter('minustospace',
      function($resolvedArgs, $exprArgs) {
        return "str_replace('-', ' ', ".$resolvedArgs.")";
      }
    );

    // Fetches a key from an array.
    // @see http://php.net/manual/en/function.key.php
    $compiler->addFilter('key',
      function($resolvedArgs, $exprArgs) {
        return "key(".$resolvedArgs.")";
      }
    );

    // Returns the current element in an array.
    // @see http://php.net/manual/en/function.current.php
    $compiler->addFilter('current',
      function($resolvedArgs, $exprArgs) {
        return "current(".$resolvedArgs.")";
      }
    );

    // Returns an array of time periods.
    $compiler->addFunction('periods', function($resolvedArgs, $exprArgs) {
      return 'ReIndex\Helper\Time::periods';
    });

    //$compiler->addFunction('arraycolumn', 'array_column');

    return $volt;
  }
);
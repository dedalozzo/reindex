[![Latest Stable Version](https://poser.pugx.org/reindex/reindex/v/stable.png)](https://packagist.org/packages/reindex/reindex)
[![Latest Unstable Version](https://poser.pugx.org/reindex/reindex/v/unstable.png)](https://packagist.org/packages/reindex/reindex)
[![Build Status](https://scrutinizer-ci.com/g/dedalozzo/reindex/badges/build.png?b=master)](https://scrutinizer-ci.com/g/dedalozzo/reindex/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dedalozzo/reindex/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dedalozzo/reindex/?branch=master)
[![License](https://poser.pugx.org/reindex/reindex/license.svg)](https://packagist.org/packages/3f/converter)
[![Total Downloads](https://poser.pugx.org/reindex/reindex/downloads.png)](https://packagist.org/packages/3f/converter)


ReIndex
========
ReIndex is an open source social network platform. The platform is still in active 
development and the [staging site](http://programmazione.me) can be unreachable or broken.

Some of the supported features are:

- Questions and answers (with comments)
- Articles (with replies)
- Updates (with links, photos and comments)
- Versioning
- Peer reviewing
- Tags and synonyms (merging is also supported)
- OAuth2 with Facebook, LinkedIn, Google and GitHub authentication
- Markdown
- Syntax highlighting
- Themes


Composer Installation
---------------------

To install ReIndex, you first need to install [Composer](http://getcomposer.org/), a Package Manager for
PHP, following these few [steps](http://getcomposer.org/doc/00-intro.md#installation-nix):

```sh
curl -s https://getcomposer.org/installer | php
```

You can run this command to easily access composer from anywhere on your system:

```sh
sudo mv composer.phar /usr/local/bin/composer
```


ReIndex Installation
--------------------
Once you have installed Composer, it's easy install EoC Client.

1. Edit your `composer.json` file, adding EoC Client to the require section:
```sh
{
    "require": {
        "reindex/reindex": "dev-master"
    },
}
```
2. Run the following command in your project root dir:
```sh
composer update
```


Documentation
-------------
The documentation can be generated using [Doxygen](http://doxygen.org). A `Doxyfile` is provided for your convenience.


Built With
----------
- [PHP](http://php.net)
- [Phalcon](https://phalconphp.com)
- [CouchDB](http://couchdb.apache.org/)
- [Redis](http://redis.io/)
- [RabbitMQ](https://www.rabbitmq.com)
- [Elephant on Couch](http://elephantoncouch.com)

Plus lots of PHP extensions and libraries. See the [composer.json](https://github.com/dedalozzo/reindex/blob/master/composer.json)
file for a complete list of dependencies.


Authors
-------
Filippo F. Fadda - <filippo.fadda@programmazione.it> - <http://www.linkedin.com/in/filippofadda>


Copyright
---------
Copyright (c) 2013-2015, REINDEX LTD
All rights reserved.


License
-------
ReIndex is licensed under the Apache License, Version 2.0 - see the LICENSE file for details.
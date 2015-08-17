ReIndex
========
ReIndex is the open source social network platform created to be used for the new version of [Programmazione.it](http://programmazione.it) developers' community.
An early version can be found at [programmazione.me](http://programmazione.me). The platform is still in active 
development and the staging site can be unreachable or broken.

Some of the supported features are:

- Questions and answers
- Posts and comments
- Versioning
- Peer reviewing
- Tags and synonyms (merging is also supported)
- Badges
- OAuth2 with Facebook, LinkedIn, Google+ and GitHub authentication
- Markdown
- Syntax highlighting


Composer Installation
---------------------

To install ReIndex, you first need to install [Composer](http://getcomposer.org/), a Package Manager for
PHP, following those few [steps](http://getcomposer.org/doc/00-intro.md#installation-nix):

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
- [Elephant On Couch](http://elephantoncouch.com)
- [jQuery](https://jquery.com/)

Plus lots of PHP extensions and libraries. See the [composer.json](https://github.com/dedalozzo/pit-press/blob/master/composer.json) 
file for a complete list of dependencies.


Authors
-------
Filippo F. Fadda - <filippo.fadda@programmazione.it> - <http://www.linkedin.com/in/filippofadda>


Copyright
---------
Copyright (c) 2013-2015, Filippo Fadda
All rights reserved.


License
-------
ReIndex is licensed under the Apache License, Version 2.0 - see the LICENSE file for details.
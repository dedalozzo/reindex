PitPress
========
PitPress is the open source QA and blogging platform used for the new version of [programmazione.it](http://programmazione.it) site.
An alpha version can be found on [programmazione.me](http://programmazione.me) site.


Composer Installation
---------------------

To install PitPress, you first need to install [Composer](http://getcomposer.org/), a Package Manager for
PHP, following those few [steps](http://getcomposer.org/doc/00-intro.md#installation-nix):

```sh
curl -s https://getcomposer.org/installer | php
```

You can run this command to easily access composer from anywhere on your system:

```sh
sudo mv composer.phar /usr/local/bin/composer
```


EoC Client Installation
-----------------------
Once you have installed Composer, it's easy install EoC Client.

1. Edit your `composer.json` file, adding EoC Client to the require section:
```sh
{
    "require": {
        "3f/pitpress": "dev-master"
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


Requirements
------------
- PHP 5.4.0 or above.


Authors
-------
Filippo F. Fadda - <filippo.fadda@programmazione.it> - <http://www.linkedin.com/in/filippofadda>


Copyright
---------
Copyright (c) 2013-2015, Filippo Fadda
All rights reserved.


License
-------
PitPress is licensed under the Apache License, Version 2.0 - see the LICENSE file for details.
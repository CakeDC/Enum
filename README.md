CakeDC Enum Plugin
==================

[![Build Status](https://img.shields.io/travis/CakeDC/Enum/master.svg?style=flat-square)](https://travis-ci.org/CakeDC/Enum)
[![Coverage](https://img.shields.io/codecov/c/github/CakeDC/Enum.svg?style=flat-square)](https://codecov.io/github/CakeDC/Enum)
[![Total Downloads](https://img.shields.io/packagist/dt/cakedc/enum.svg?style=flat-square)](https://packagist.org/packages/cakedc/enum)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

Enumeration list for [CakePHP 3](http://cakephp.org).

Versions and branches
---------------------

| CakePHP         | CakeDC Enum Plugin                                         |  Tag  | Notes    |
| :-------------: | :------------------------:                                 | :--:  | :----    |
| ^3.7            | [master](https://github.com/cakedc/enum/tree/master)       | 1.5.0 | stable   |
| ^3.7            | [develop](https://github.com/cakedc/enum/tree/develop)     | -     | unstable |
| ^3.6            | [master](https://github.com/cakedc/enum/tree/1.4.0)       | 1.4.0 | stable   |
| 3.1 - 3.5       |                                                            | 1.3.0 | stable   |


Install
-------

Using [Composer](http://getcomposer.org):

```
composer require cakedc/enum:1.5.0
```

You then need to load the plugin. You can use the shell command:

```
bin/cake plugin load CakeDC/Enum
```

or by manually adding statement shown below to `bootstrap.php`:

```php
Plugin::load('CakeDC/Enum');
```

Requirements
------------

* CakePHP 3.7+
* PHP 5.6+

Documentation
-------------

For documentation, as well as tutorials, see the [Docs](Docs/Home.md) directory of this repository.

Support
-------

For bugs and feature requests, please use the [issues](https://github.com/CakeDC/Enum/issues) section of this repository.

Commercial support is also available, [contact us](http://cakedc.com/contact) for more information.

Contributing
------------

This repository follows the [CakeDC Plugin Standard](http://cakedc.com/plugin-standard). If you'd like to contribute new features, enhancements or bug fixes to the plugin, please read our [Contribution Guidelines](http://cakedc.com/contribution-guidelines) for detailed instructions.

License
-------

Copyright 2015 - 2019 Cake Development Corporation (CakeDC). All rights reserved.

Licensed under the [MIT](http://www.opensource.org/licenses/mit-license.php) License. Redistributions of the source code included in this repository must retain the copyright notice found in each file.

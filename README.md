CakeDC Enum Plugin
==================

[![Build Status](https://img.shields.io/travis/CakeDC/Enum/master.svg?style=flat-square)](https://travis-ci.org/CakeDC/Enum)
[![Coverage](https://img.shields.io/coveralls/CakeDC/Enum/master.svg?style=flat-square)](https://coveralls.io/r/CakeDC/Enum)
[![Total Downloads](https://img.shields.io/packagist/dt/cakedc/enum.svg?style=flat-square)](https://packagist.org/packages/cakedc/enum)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

{{@TODO description}}

Install
-------

Using [Composer][composer]:

```
composer require cakedc/enum:dev-master
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

* CakePHP 3.0+
* PHP 5.4.16+

Documentation
-------------

For documentation, as well as tutorials, see the [Docs](Docs/Home.md) directory of this repository.

Roadmap
------

* 3.0.0 Migration to CakePHP 3.x
* 3.0.1 General improvements
  * Unit test coverage improvements
  * Refactor UsersTable to Behavior
  * Add google authentication
  * Add captcha
  * Link social accounts in profile

Support
-------

For bugs and feature requests, please use the [issues](https://github.com/CakeDC/users/issues) section of this repository.

Commercial support is also available, [contact us](http://cakedc.com/contact) for more information.

Contributing
------------

This repository follows the [CakeDC Plugin Standard](http://cakedc.com/plugin-standard). If you'd like to contribute new features, enhancements or bug fixes to the plugin, please read our [Contribution Guidelines](http://cakedc.com/contribution-guidelines) for detailed instructions.

License
-------

Copyright 2015 Cake Development Corporation (CakeDC). All rights reserved.

Licensed under the [MIT](http://www.opensource.org/licenses/mit-license.php) License. Redistributions of the source code included in this repository must retain the copyright notice found in each file.

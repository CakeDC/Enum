Home
====

Requirements
------------

* CakePHP 5.3+
* PHP 8.2+

Documentation
-------------

* [Installation](Documentation/Installation.md)
* [Configuration](Documentation/Configuration.md)
* [Examples](Documentation/Examples.md)

Upgrading to 3.3.0
------------------

* Requirements raised to CakePHP 5.3+ and PHP 8.2+.
* The default validation error message now shows the given value and the expected ones, e.g. `Invalid value 'Drafted', expected values are 'PUBLIC', 'DRAFT' or 'ARCHIVE'.` If your tests or UI rely on the previous message, update them or set the list `errorMessage` option to a fixed message. See [Validation Error Message](Documentation/Configuration.md#validation-error-message).
* New `validation` option adds the list check to table validators, so invalid values are reported by `newEntity()` and `patchEntity()`. It is disabled by default. See [Validation Configuration](Documentation/Configuration.md#validation-configuration).

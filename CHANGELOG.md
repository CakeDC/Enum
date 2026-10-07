Changelog
=========
* 3.3.0
  * Added `validation` option to add list rules to table validators, so invalid values are reported by `newEntity()` and `patchEntity()`
  * Improved default validation error message: shows the given value and the expected values (#39)
  * Added `:value` and `:expected` placeholders support to `errorMessage`
  * Application rules validate against the raw strategy list, unaffected by `translate` and `nested` options
  * Requires CakePHP ^5.3 and PHP >= 8.2
  * CI moved to GitHub Actions (PHP 8.2 - 8.4), code style and PHPStan checks

* 3.2.0
  * fixed enum validation
  * fixed property conflict in lookup strategy
  * fixed migration

* 3.1.1
  * fixed: Field with null values no longer work even set opion allowEmpty as true

* 3.1.0
  * fixed issue with $constant beeing of type /Cake/ORM/Entity
  * fixed issue with missing support for non hydrated results in src/Model/Behavior/Strategy/ConstStrategy.php

* 3.0.0
  * CakePHP 5.x support

* 2.2.2
  * Fixed migration
  * Fixed property conflict in lookup strategy
* 2.2.1
  * Fix plugin name
* 2.2.0
  * Backport #57 fix to 2.next branch (cake4) by @jay-eventrac in #58
* 2.0.3
  * Fix ConstStrategy for associations queries without record
* 2.0.1
  * Constant Strategy Improvements
* 2.0.0 CakePHP 4.x support

* 1.2.0
  * Documentation improved
  * Allow saving entity with no value for enum field
  * Allow returning enum list in nested form
  * Unit tests fixed/improved

* 1.1.0
  * Add translations support
  * Docs improved

* 1.0.1
  * Added 3rd party strategy support
  * Improved documentation

* 1.0.0
  * Updated composer.json

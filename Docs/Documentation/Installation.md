# Installation

## Composer

```
composer require cakedc/enum:dev-master
```

## Load the Plugin

Ensure the Enum Plugin is loaded in your config/bootstrap.php file

```php
Plugin::load('CakeDC/Enum');
```
or
```php
Plugin::loadAll();
```

## Creating Required Tables

You need to run this command to create the table:

```
bin/cake migrations migrate -p CakeDC/Enum
```

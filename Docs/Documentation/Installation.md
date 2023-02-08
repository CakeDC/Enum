# Installation

## Composer

```
composer require cakedc/enum:3.0.0-dev
```

## Creating Required Tables

You need to run this command to create the table:

```
bin/cake migrations migrate -p CakeDC/Enum
```

## Load the Plugin

Ensure the Enum Plugin is loaded in your `Application::bootstrap()` method:

```php
$this->addPlugin('CakeDC/Enum');
```

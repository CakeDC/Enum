# Configuration

## Behavior, Model and Helper Configuration

Firstly you need to add this in your table class ```$this->addBehavior('CakeDC/Enum.Enum');``` to load the behavior, after that you need to set the configurations, you can choose one or more to use in the same table class. When you get this done, you need to load the helper in your view class using ```$this->loadHelper('CakeDC/Enum.Enum');```

## Supported Types of Enum

* **Lookups:** This type of configuration, you'll need to create a migration to populate the enum table, so every time it'll to do a find to get your options
* **Const:** This is useful when you have some defined const in your table class, so it'll get all consts based on the prefix that you'll set in the configs of the behavior
* **Config:** Here you'll write a configuration with the ```Configure``` class using **"CakeDC/Enum"** as key.

## Behavior Configuration

You always need to set the alias and the prefix, as this is essential for the plugin to work. These options are explained below:

* **priority**
* **status**
* **category**

### Lookups Configuration

In the lookups configuration you don't need to set the strategy, because lookups is the default strategy used in the enum plugin.

```php
$this->addBehavior('CakeDC/Enum.Enum', ['lists' => [
    'priority' => [
        'prefix' => 'PRIORITY'
    ],
]]);
```

### Const Configuration

In this strategy, it will catch all variables by constant, in this case you need to have in your table the const defined so ```const STATUS_SOMETHING = "Example" ```, here you can choose if you want value is lowercase using this ``` 'lowercase' => true ``` by default it use as it was defined. Use `className` to configure the specific class where the constants are placed.

```php
$this->addBehavior('CakeDC/Enum.Enum', ['lists' => [
    'status' => [
        'strategy' => 'const',
        'prefix' => 'STATUS'
    ],
]]);
```

### Config Configuration

Using this strategy you'll need to add the values to key **"CakeDC/Enum"** in the global Configure class, for example:

```php
Configure::write('CakeDC/Enum', [
    'category' => [
        'CakePHP',
        'Open Source Software',
    ]
]);
```
Then you can configure the behaviour configuration like so:

```php
$this->addBehavior('CakeDC/Enum.Enum', ['lists' => [
    'category' => [
        'strategy' => 'config',
        'prefix' => 'category',
    ],
]]);
```

### Third-Party Strategy Configuration

You can also use your own strategy to prepare the enums. Example using a third-party strategy via `classMap` config param:

```php
$this->addBehavior('CakeDC/Enum.Enum', [
    'classMap' => [
        'property' => 'Other\Enum\Model\Behavior\Strategy\PropertyStrategy'
    ],
    'lists' => [
        'category' => [
            'strategy' => 'property'
        ],
    ]
]);
```

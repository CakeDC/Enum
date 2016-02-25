# Configuration

## Behavior, Model and Helper Configuration

Firstly you need to add this in your table class ```$this->addBehavior('CakeDC/Enum.Enum');``` to load the behavior, after that you need to set the configurations, you can choose one or more to use in the same table class. When you get this done, you need to load the helper in your view class using ```$this->loadHelper('CakeDC/Enum.Enum');```

## Supported Types of Enum

* **Lookups:** This type of configuration, you'll need to create a migration to populate the enum table, so every time it'll to do a find to get your options
* **Const:** This is useful when you have some defined const in your table class, so it'll get all consts based on the prefix that you'll set in the configs of the behavior
* **Config:** Here you'll write a configuration with the ```Configure``` class using **"CakeDC/Enum"** as key.

## Behavior Configuration

You always need to set the alias and the prefix, So this is essential for the plugin to work. In use case we will show three alias: 

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

In this strategy, it will catch all variables constant, in this case you need to have in your table the const defined so ```const STATUS_SOMETHING = "Example" ```, here you can choose if you want value is lowercase using this ``` 'lowercase' => true ``` by default it use as it was defined.

```php
$this->addBehavior('CakeDC/Enum.Enum', ['lists' => [
    'status' => [
        'strategy' => 'const',
        'prefix' => 'STATUS'
    ],
]]);
```

### Config Configuration

This strategy you need to write in the configuration class the values using **"CakeDC/Enum"** as key for example: 

```php
Configure::write('CakeDC/Enum', [
    'category' => [
        'CakePHP',
        'Open Source Software',
    ]
]);
```
Then you can set the behavior configuration so: 

```php
$this->addBehavior('CakeDC/Enum.Enum', ['lists' => [
    'category' => [
        'strategy' => 'config',
        'prefix' => 'category',
    ],
]]);
```
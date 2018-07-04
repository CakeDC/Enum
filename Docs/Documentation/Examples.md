# Examples

## Lookups Example

Firstly you need to create a migration to populate the database for you create the migration you need to run this command:

``` bin/cake migrations create MyCustomMigration ``` and in the change method you need to create the entries, for example:

```php
public function change()
{
    $LookupsTable = \Cake\ORM\TableRegistry::get('CakeDC/Enum.Lookups');

    $lookups = $LookupsTable->newEntity();
    $lookups->name = 'URGENT ';
    $lookups->label = 'Urgent';
    $lookups->prefix = 'PRIORITY';
    $LookupsTable->save($lookups);

    $lookups = $LookupsTable->newEntity();
    $lookups->name = 'HIGH ';
    $lookups->label = 'High';
    $lookups->prefix = 'PRIORITY';
    $LookupsTable->save($lookups);

    $lookups = $LookupsTable->newEntity();
    $lookups->name = 'NORMAL ';
    $lookups->label = 'Normal';
    $lookups->prefix = 'PRIORITY';
    $LookupsTable->save($lookups);
}
```

After that you need to set the configuration in the behavior load, for example:

```php
$this->addBehavior('CakeDC/Enum.Enum', ['lists' => [
    'priority' => [
        'prefix' => 'PRIORITY'
    ],
]]);
```

For select lists, you can send a list of enums to the view from your controller:

```
$this->set('priorities', $this->Articles->enum('priority'));
```

If you use the plural variable name, you don't need to do anything in special in the view:

```php
<?= $this->Form->input('priorities'); ?>
```

## Const Example

Here you need to create some constants variables in your table class and configure your behavior to get them for example:

```php
class ArticlesTable extends Table
{
    const STATUS_PUBLIC = 'Published';
    const STATUS_DRAFT = 'Drafted';
    const STATUS_ARCHIVE = 'Archived';

    public function initialize(array $config)
    {
        $this->addBehavior('CakeDC/Enum.Enum', ['lists' => [
            'status' => [
                'strategy' => 'const',
                'prefix' => 'STATUS'
            ]
        ]]);
    }
}
```

For select lists, you can send a list of enums to the view from your controller:

```
$this->set('statuses', $this->Articles->enum('status'));
```

If you use the a plural variable name, you don't need to do anything special in the view:

```php
<?= $this->Form->input('statuses'); ?>
```

You can change the default class and choose a new class to put your constaint variables like that:

```php
class ArticlesTable extends Table
{
    public function initialize(array $config)
    {
        $this->addBehavior('CakeDC/Enum.Enum', ['lists' => [
            'status' => [
                'strategy' => 'const',
                'prefix' => 'STATUS',
                'className' => 'App\\Model\\Entity\\Article'
            ]
        ]]);
    }
}
```

so, your constaints need to be in the Article entity class.

## Config Example

Here you need to create some constants variables in you table class and configure your behavior to get them for example:

```php
Configure::write(ConfigStrategy::KEY . 'category', [ // or 'CakeDC/Enum.category' as key
    'Published',
    'Drafted',
    'Archived'
]);
```

For select lists, you can send a list of enums to the view from your controller:

```
$this->set('categories', $this->Articles->enum('category'));
```

If you use the a plural variable name, you don't need to do anything special in the view:

```php
<?= $this->Form->input('categories'); ?>
```

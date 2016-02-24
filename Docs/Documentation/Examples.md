Examples
=============

###Lookups Example

Firstly you need to create a migration to populate the database for you create the migration you need to run this command: 

``` bin/cake migrations create MyCustomMigration ``` and in the change method you need to create the entries, for example:

```php
    public function change()
    {
        $LookupsTable = \Cake\ORM\TableRegistry::get('CakeDC/Enum.Lookups');
        $lookups = $LookupsTable->newEntity();

        $lookups = $LookupsTable->newEntity();
        $lookups->name = 'URGENT ';
        $lookups->label = 'Urgent';
        $lookups->prefix = 'PRIORITY';
        $LookupsTable->save($lookups);

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

Then in the view you will use the enum helper to load your select box for example:

```php
    <?= $this->Enum->input('field', 'Articles', ['alias' => 'priority']); ?>
```

or 

```php
    <?= $this->Enum->input('Articles.field', '', ['alias' => 'priority']); ?>
```

###Const Example

Here you need to create some constants variables in you table class and configure you behavior to get them for example: 

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

Then in the view you will use the enum helper to load your select box for example:

```php
    <?= $this->Enum->input('field', 'Articles', ['alias' => 'status']); ?>
```

or 

```php
    <?= $this->Enum->input('Articles.field', '', ['alias' => 'status']); ?>
```

###Config Example

Here you need to create some constants variables in you table class and configure you behavior to get them for example: 

```php
    Configure::write(ConfigStrategy::KEY, [ // or 'CakeDC/Enum' as key
        'category' => [
            'Published',
            'Drafted',
            'Archived'
        ],
    ]);
```

Then in the view you will use the enum helper to load your select box for example:

```php
    <?= $this->Enum->input('field', 'Articles', ['alias' => 'category']); ?>
```

or 

```php
    <?= $this->Enum->input('Articles.field', '', ['alias' => 'category']); ?>
```
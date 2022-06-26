<?php
declare(strict_types=1);

/**
 * Abstract schema for CakePHP tests.
 *
 * This format resembles the existing fixture schema
 * and is converted to SQL via the Schema generation
 * features of the Database package.
 */
return [
    [
        'table' => 'enum_articles',
        'columns' => [
            'id' => ['type' => 'integer'],
            'title' => ['type' => 'string'],
            'body' => ['type' => 'text'],
            'priority' => ['type' => 'string'],
            'status' => ['type' => 'string'],
            'article_category' => ['type' => 'integer'],
        ],
        'constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
    ],
    [
        'table' => 'enum_lookups',
        'columns' => [
            'id' => ['type' => 'integer'],
            'label' => ['type' => 'string'],
            'prefix' => ['type' => 'string'],
            'name' => ['type' => 'string'],
        ],
        'constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
    ],
];

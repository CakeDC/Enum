<?php
namespace Enum\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class ArticlesFixture extends TestFixture
{
    public $table = 'enum_articles';

    public $fields = [
        'id' => ['type' => 'integer'],
        'title' => ['type' => 'string'],
        'body' => ['type' => 'text'],
        'priority_id' => ['type' => 'integer'],
        'status_id' => ['type' => 'integer'],
        'article_category_id' => ['type' => 'integer'],
        '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
        '_options' => ['engine' => 'InnoDB', 'collation' => 'utf8_general_ci'],
    ];

    public $records = [
        [
            'title' => 'Dummy article',
            'body' => '',
            'priority_id' => 2,
            'status_id' => 4,
            'article_category_id' => 6
        ],
    ];
}
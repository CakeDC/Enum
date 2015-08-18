<?php
namespace Enum\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class LookupsFixture extends TestFixture
{
    public $table = 'enum_lookups';

    public $fields = [
        'id' => ['type' => 'integer'],
        'label' => ['type' => 'string'],
        'prefix' => ['type' => 'string'],
        'name' => ['type' => 'string'],
    ];

    public $records = [
        [
            'id' => 1,
            'label' => 'Urgent',
            'prefix' => 'PRIORITY',
            'name' => 'URGENT',
        ],
        [
            'id' => 2,
            'label' => 'High',
            'prefix' => 'PRIORITY',
            'name' => 'HIGH',
        ],
        [
            'id' => 3,
            'label' => 'Normal',
            'prefix' => 'PRIORITY',
            'name' => 'NORMAL',
        ],
        [
            'id' => 4,
            'label' => 'Published',
            'prefix' => 'ARTICLE_STATUS',
            'name' => 'PUBLIC',
        ],
        [
            'id' => 5,
            'label' => 'Drafted',
            'prefix' => 'ARTICLE_STATUS',
            'name' => 'DRAFT',
        ],
        [
            'id' => 6,
            'label' => 'Archived',
            'prefix' => 'ARTICLE_STATUS',
            'name' => 'ARCHIVE',
        ],
        [
            'id' => 7,
            'label' => 'CakePHP',
            'prefix' => 'ARTICLE_CATEGORY',
            'name' => 'CAKEPHP',
        ],
        [
            'id' => 8,
            'label' => 'Open Source Software',
            'prefix' => 'ARTICLE_CATEGORY',
            'name' => 'OSS',
        ],
    ];
}

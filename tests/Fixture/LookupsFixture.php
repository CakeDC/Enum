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
            'label' => 'Urgent',
            'prefix' => 'PRIORITY',
            'name' => 'URGENT',
        ],
        [
            'label' => 'High',
            'prefix' => 'PRIORITY',
            'name' => 'HIGH',
        ],
        [
            'label' => 'Normal',
            'prefix' => 'PRIORITY',
            'name' => 'NORMAL',
        ],
        [
            'label' => 'Published',
            'prefix' => 'ARTICLE_STATUS',
            'name' => 'PUBLIC',
        ],
        [
            'label' => 'Drafted',
            'prefix' => 'ARTICLE_STATUS',
            'name' => 'DRAFT',
        ],
        [
            'label' => 'Archived',
            'prefix' => 'ARTICLE_STATUS',
            'name' => 'ARCHIVE',
        ],
        [
            'label' => 'CakePHP',
            'prefix' => 'ARTICLE_CATEGORY',
            'name' => 'CAKEPHP',
        ],
        [
            'label' => 'Open Source Software',
            'prefix' => 'ARTICLE_CATEGORY',
            'name' => 'OSS',
        ],
    ];
}
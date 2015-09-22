<?php

/**
 * Copyright 2015, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2015, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\Enum\Test\Fixture;

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
            'label' => 'CakePHP',
            'prefix' => 'ARTICLE_CATEGORY',
            'name' => 'CAKEPHP',
        ],
        [
            'id' => 5,
            'label' => 'Open Source Software',
            'prefix' => 'ARTICLE_CATEGORY',
            'name' => 'OSS',
        ],
    ];
}

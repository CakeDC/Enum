<?php

/**
 * Copyright 2015, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2015, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\Enum\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class ArticlesFixture extends TestFixture
{
    public $table = 'enum_articles';

    public $fields = [
        'id' => ['type' => 'integer'],
        'title' => ['type' => 'string'],
        'body' => ['type' => 'text'],
        'priority' => ['type' => 'integer'],
        'status' => ['type' => 'integer'],
        'article_category' => ['type' => 'integer'],
        '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
        '_options' => ['engine' => 'InnoDB', 'collation' => 'utf8_general_ci'],
    ];

    public $records = [
        [
            'id' => 1,
            'title' => 'Dummy article',
            'body' => '',
            'priority' => 'HIGH',
            'status' => 'PUBLIC',
            'article_category' => 6
        ],
    ];
}

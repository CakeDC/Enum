<?php
declare(strict_types=1);

/**
 * Copyright 2015 - 2023, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2015 - 2023, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\Enum\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class ArticlesFixture extends TestFixture
{
    public string $table = 'enum_articles';

    public array $fields = [
        'id' => ['type' => 'integer'],
        'title' => ['type' => 'string'],
        'body' => ['type' => 'text'],
        'priority' => ['type' => 'string'],
        'status' => ['type' => 'string'],
        'article_category' => ['type' => 'integer'],
        '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
    ];

    public array $records = [
        [
            'title' => 'Dummy article',
            'body' => '',
            'priority' => 'HIGH',
            'status' => 'PUBLIC',
            'article_category' => 6,
        ],
    ];
}

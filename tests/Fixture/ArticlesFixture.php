<?php
declare(strict_types=1);

/**
 * Copyright 2015 - 2024, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2015 - 2024, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\Enum\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class ArticlesFixture extends TestFixture
{
    public string $table = 'enum_articles';

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

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

class LookupsFixture extends TestFixture
{
    public string $table = 'enum_lookups';

    public array $records = [
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

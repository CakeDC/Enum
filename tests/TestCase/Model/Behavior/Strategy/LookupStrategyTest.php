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

namespace CakeDC\Enum\Test\TestCase\Model\Behavior\Strategy;

use Cake\ORM\Table;
use Cake\TestSuite\TestCase;
use CakeDC\Enum\Model\Behavior\Strategy\LookupStrategy;

class LookupStrategyTest extends TestCase
{
    /**
     * @var \CakeDC\Enum\Model\Behavior\Strategy\LookupStrategy|mixed
     */
    public mixed $Strategy;

    public array $fixtures = [
        'plugin.CakeDC/Enum.Lookups',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->Strategy = new LookupStrategy('priority', new Table());
        $this->Strategy->initialize(['prefix' => 'PRIORITY']);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->Strategy);
    }

    public function testEnum()
    {
        $result = $this->Strategy->enum();
        $expected = [
            'URGENT' => 'Urgent',
            'HIGH' => 'High',
            'NORMAL' => 'Normal',
        ];
        $this->assertEquals($expected, $result);
    }
}

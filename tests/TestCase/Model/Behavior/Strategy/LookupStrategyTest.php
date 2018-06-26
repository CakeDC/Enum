<?php

/**
 * Copyright 2015 - 2018, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2015 - 2018, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\Enum\Test\TestCase\Model\Behavior\Strategy;

use CakeDC\Enum\Model\Behavior\Strategy\LookupStrategy;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class LookupStrategyTest extends TestCase
{
    public $fixtures = [
        'plugin.CakeDC/Enum.Lookups',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->Strategy = new LookupStrategy('priority', new Table());
        $this->Strategy->initialize(['prefix' => 'PRIORITY']);
    }

    public function tearDown()
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

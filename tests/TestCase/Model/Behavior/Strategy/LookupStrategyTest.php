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
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->Strategy);
    }

    public function testHasPrefix()
    {
        $this->assertTrue($this->Strategy->hasPrefix('priority'));
        $this->assertTrue($this->Strategy->hasPrefix('article_category'));
        $this->assertFalse($this->Strategy->hasPrefix('status'));
    }

    public function testListPrefixes()
    {
        $result = $this->Strategy->listPrefixes();
        $expected = [
            'PRIORITY',
            'ARTICLE_CATEGORY'
        ];
        $this->assertEquals($expected, $result);
    }

    public function testEnum()
    {
        $result = $this->Strategy->enum();
        $expected = [
            'PRIORITY_URGENT' => 'Urgent',
            'PRIORITY_HIGH' => 'High',
            'PRIORITY_NORMAL' => 'Normal',
        ];
        $this->assertEquals($expected, $result);
    }
}

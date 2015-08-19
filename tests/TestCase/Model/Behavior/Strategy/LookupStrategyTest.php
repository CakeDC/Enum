<?php

namespace Enum\Test\TestCase\Model\Behavior\Strategy;

use Cake\ORM\Table;
use Cake\TestSuite\TestCase;
use Enum\Model\Behavior\Strategy\LookupStrategy;

class LookupStrategyTest extends TestCase
{
    public $fixtures = [
        'plugin.Enum.lookups',
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

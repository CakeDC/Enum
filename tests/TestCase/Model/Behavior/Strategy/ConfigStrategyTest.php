<?php
namespace CakeDC\Enum\Test\TestCase\Model\Behavior\Strategy;

use Cake\Core\Configure;
use Cake\ORM\Table;
use Cake\TestSuite\TestCase;
use CakeDC\Enum\Model\Behavior\Strategy\ConfigStrategy;

class ConfigStrategyTest extends TestCase
{
    public $Strategy;

    public function setUp()
    {
        parent::setUp();
        Configure::write(ConfigStrategy::KEY, [
            'status' => [
                'Published',
                'Drafted',
                'Archived'
            ],
        ]);
        $this->Strategy = new ConfigStrategy('status', new Table());
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->Strategy);
    }

    public function testListPrefixes()
    {
        $result = $this->Strategy->listPrefixes();
        $expected = ['STATUS'];
        $this->assertEquals($expected, $result);
    }

    public function testEnum()
    {
        $this->Strategy->config('prefix', 'STATUS');
        $result = $this->Strategy->enum();
        $expected = [
            'Published',
            'Drafted',
            'Archived',
        ];
        $this->assertEquals($expected, $result);
    }
}

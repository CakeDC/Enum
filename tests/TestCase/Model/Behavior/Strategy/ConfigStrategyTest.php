<?php
declare(strict_types=1);
/**
 * Copyright 2015 - 2019, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2015 - 2019, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

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
            'STATUS' => [
                'Published',
                'Drafted',
                'Archived',
            ],
        ]);
        $this->Strategy = new ConfigStrategy('status', new Table());
        $this->Strategy->initialize(['prefix' => 'STATUS']);
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
            'Published',
            'Drafted',
            'Archived',
        ];
        $this->assertEquals($expected, $result);
    }
}

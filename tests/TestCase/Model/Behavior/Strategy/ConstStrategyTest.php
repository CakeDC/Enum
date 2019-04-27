<?php

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

use CakeDC\Enum\Model\Behavior\Strategy\ConstStrategy;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\TestSuite\TestCase;

class ArticlesTable extends Table
{

    const EXTRA_VALUE = 'Extra';

    const STATUS_PUBLIC = 'Published';
    const STATUS_DRAFT = 'Drafted';
    const STATUS_ARCHIVE = 'Archived';
}

class Article extends Entity
{

    const EXTRA_VALUE = 'Extra';

    const STATUS_PUBLIC = 'Published';
    const STATUS_DRAFT = 'Drafted';
    const STATUS_ARCHIVE = 'Archived';
}

class ConstStrategyTest extends TestCase
{
    public $StrategyTable;

    public $StrategyEntity;

    public function setUp()
    {
        parent::setUp();
        $this->StrategyTable = new ConstStrategy('status', new ArticlesTable());
        $this->StrategyTable->initialize(['prefix' => 'STATUS', 'lowercase' => true]);

        $this->StrategyEntity = new ConstStrategy('status', new ArticlesTable());
        $this->StrategyEntity->initialize([
            'prefix' => 'STATUS',
            'lowercase' => true,
            'className' => 'CakeDC\\Enum\\Test\\TestCase\\Model\\Behavior\\Strategy\\Article',
        ]);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->StrategyTable);
        unset($this->StrategyEntity);
    }

    public function testEnumTable()
    {
        $result = $this->StrategyTable->enum();
        $expected = [
            'public' => 'Published',
            'draft' => 'Drafted',
            'archive' => 'Archived',
        ];
        $this->assertEquals($expected, $result);

        // Cached list
        $this->assertEquals($expected, $this->StrategyTable->enum());
    }

    public function testEnumEntity()
    {
        $result = $this->StrategyEntity->enum();
        $expected = [
            'public' => 'Published',
            'draft' => 'Drafted',
            'archive' => 'Archived',
        ];
        $this->assertEquals($expected, $result);

        // Cached list
        $this->assertEquals($expected, $this->StrategyEntity->enum());
    }
}

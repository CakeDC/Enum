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

namespace CakeDC\Enum\Test\TestCase\Model\Behavior\Strategy;

use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\TestSuite\TestCase;
use CakeDC\Enum\Model\Behavior\Strategy\ConstStrategy;

// @codingStandardsIgnoreStart
class ArticlesTable extends Table
{
    public const EXTRA_VALUE = 'Extra';

    public const STATUS_PUBLIC = 'Published';
    public const STATUS_DRAFT = 'Drafted';
    public const STATUS_ARCHIVE = 'Archived';
}

class Article extends Entity
{
    public const EXTRA_VALUE = 'Extra';

    public const STATUS_PUBLIC = 'Published';
    public const STATUS_DRAFT = 'Drafted';
    public const STATUS_ARCHIVE = 'Archived';
}
class ConstStrategyTest extends TestCase
// @codingStandardsIgnoreEnd
{
    public ConstStrategy $StrategyTable;

    public ConstStrategy $StrategyEntity;

    public function setUp(): void
    {
        parent::setUp();
        $this->StrategyTable = new ConstStrategy('status', new ArticlesTable());
        $this->StrategyTable->initialize(['prefix' => 'STATUS', 'lowercase' => true]);

        $this->StrategyEntity = new ConstStrategy('status', new ArticlesTable());
        $this->StrategyEntity->initialize([
            'prefix' => 'STATUS',
            'lowercase' => true,
            'className' => Article::class,
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->StrategyTable);
        unset($this->StrategyEntity);
    }

    /**
     * @throws \ReflectionException
     */
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

    /**
     * @throws \ReflectionException
     */
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

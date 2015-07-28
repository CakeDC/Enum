<?php
namespace Enum\Test\TestCase\Model\Behavior;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class EnumBehaviorTest extends TestCase
{
    public $fixtures = [
        'plugin.Enum.articles',
        'plugin.Enum.lookups',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->Articles = TableRegistry::get('Enum.Articles');
    }

    public function tearDown()
    {
        parent::tearDown();
        TableRegistry::clear();
    }

    public function provideBasicConfiguration()
    {
        $expected = [
            'strategy' => 'lookup',
            'table' => 'Enum.Lookups',
            'groups' => [
                'priority' => [
                    'prefix' => 'PRIORITY',
                ],
                'status' => [
                    'prefix' => 'ARTICLE_STATUS',
                ],
                'category' => [
                    'prefix' => 'ARTICLE_CATEGORY'
                ],
            ],
        ];

        return [
            [
                [
                    'groups' => [
                        'priority',
                        'status',
                        'category',
                    ],
                ],
                $expected
            ],
            [
                [
                    'groups' => [
                        'priority',
                        'status' => 'article_status',
                        'category' => 'ARTICLE_CATEGORY',
                    ],
                ],
                $expected
            ],
        ];
    }

    /**
     * @dataProvider provideBasicConfiguration
     */
    public function testBasicConfiguration(array $config, array $expected)
    {
        $this->Articles->addBehavior('Enum.Enum', $config);

        $result = $this->Articles->behaviors()->Enum->config();
        $this->assertEquals($expected, $result);
    }
}
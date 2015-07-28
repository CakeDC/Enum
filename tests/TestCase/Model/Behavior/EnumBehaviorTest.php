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

        $this->Articles = TableRegistry::get('Enum.Articles', ['table' => 'enum_articles']);
        $this->Articles->addBehavior('Enum.Enum', ['providers' => [
            'priority',
            'status',
            'category',
        ]]);
    }

    public function tearDown()
    {
        parent::tearDown();
        TableRegistry::clear();
    }

    public function provideBasicConfiguration()
    {
        $expected = [
            'defaultStrategy' => 'lookup',
            'implementedMethods' => ['enum' => 'enum'],
            'providers' => [
                'priority' => [
                    'strategy' => 'lookup',
                    'prefix' => 'PRIORITY',
                ],
                'status' => [
                    'strategy' => 'lookup',
                    'prefix' => 'ARTICLE_STATUS',
                ],
                'category' => [
                    'strategy' => 'lookup',
                    'prefix' => 'ARTICLE_CATEGORY'
                ],
            ],
        ];

        return [
            [
                [
                    'providers' => [
                        'priority',
                        'status',
                        'category',
                    ],
                ],
                $expected
            ],
            [
                [
                    'providers' => [
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
        TableRegistry::clear();
        $Articles = TableRegistry::get('Enum.Articles', ['table' => 'enum_articles']);
        $Articles->addBehavior('Enum.Enum', $config);
        $result = $Articles->behaviors()->Enum->config();
        $this->assertEquals($expected, $result);
    }

    public function provideBasicLookups()
    {
        return [
            [
                'priority',
                [
                    'PRIORITY_URGENT' => 'Urgent',
                    'PRIORITY_HIGH' => 'High',
                    'PRIORITY_NORMAL' => 'Normal',
                ]
            ],
            [
                'status',
                [
                    'ARTICLE_STATUS_PUBLIC' => 'Published',
                    'ARTICLE_STATUS_DRAFT' => 'Drafted',
                    'ARTICLE_STATUS_ARCHIVE' => 'Archived',
                ]
            ],
            [
                'category',
                [
                    'ARTICLE_CATEGORY_CAKEPHP' => 'CakePHP',
                    'ARTICLE_CATEGORY_OSS' => 'Open Source Software',
                ]
            ],
        ];
    }

    /**
     * @dataProvider provideBasicLookups
     */
    public function testBasicLookups($group, $expected)
    {
        $result = $this->Articles->enum($group);
        $this->assertEquals($expected, $result);
    }
}
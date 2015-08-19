<?php
namespace Enum\Test\TestCase\Model\Behavior;

use Cake\Core\Configure;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class ArticlesTable extends Table
{
    const STATUS_PUBLIC = 'Published';
    const STATUS_DRAFT = 'Drafted';
    const STATUS_ARCHIVE = 'Archived';

    public function initialize(array $config)
    {
        $this->addBehavior('Enum.Enum', ['lists' => [
            'priority' => ['errorMessage' => 'Invalid priority'],
            'status' => ['strategy' => 'const'],
            'category' => ['strategy' => 'config'],
        ]]);
    }
}

class EnumBehaviorTest extends TestCase
{
    public $fixtures = [
        'plugin.Enum.articles',
        'plugin.Enum.lookups',
    ];

    protected $Articles;

    public function setUp()
    {
        parent::setUp();

        Configure::write('Enum', [
            'ARTICLE_CATEGORY' => [
                'CakePHP',
                'Open Source Software',
            ]
        ]);

        $this->Articles = TableRegistry::get('Enum.Articles', [
            'className' => ArticlesTable::class,
            'table' => 'enum_articles'
        ]);
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
            'lists' => [
                'priority' => [
                    'strategy' => 'lookup',
                    'prefix' => 'PRIORITY',
                    'field' => 'priority',
                    'errorMessage' => 'Invalid priority'
                ],
                'status' => [
                    'strategy' => 'const',
                    'prefix' => 'STATUS',
                    'field' => 'status',
                    'errorMessage' => 'The provided value is invalid'
                ],
                'category' => [
                    'strategy' => 'config',
                    'prefix' => 'ARTICLE_CATEGORY',
                    'field' => 'category',
                    'errorMessage' => 'The provided value is invalid'
                ],
            ],
        ];

        return [
            [
                [
                    'lists' => [
                        'priority' => ['errorMessage' => 'Invalid priority'],
                        'status' => ['strategy' => 'const', 'prefix' => 'STATUS'],
                        'category' => ['strategy' => 'config', 'prefix' => 'ARTICLE_CATEGORY'],
                    ],
                ],
                $expected
            ],
            [
                [
                    'lists' => [
                        'priority' => ['errorMessage' => 'Invalid priority'],
                        'status' => ['strategy' => 'const', 'prefix' => 'STATUS'],
                        'category' => ['strategy' => 'config', 'prefix' => 'ARTICLE_CATEGORY'],
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
                    'STATUS_PUBLIC' => 'Published',
                    'STATUS_DRAFT' => 'Drafted',
                    'STATUS_ARCHIVE' => 'Archived',
                ]
            ],
            [
                'category',
                [
                    'CakePHP',
                    'Open Source Software',
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

    public function provideBuildRules()
    {
        return [
            [
                [
                    'priority' => 'PRIORITY_URGENT',
                    'status' => 'STATUS_DRAFT',
                    'category' => 2,
                ],
                [
                    'category' => ['isValidCategory' => 'The provided value is invalid'],
                ]
            ],
            [
                [
                    'priority' => 'Urgent',
                    'status' => 'Drafted',
                    'category' => 1,
                ],
                [
                    'priority' => ['isValidPriority' => 'Invalid priority'],
                    'status' => ['isValidStatus' => 'The provided value is invalid'],
                ]
            ]
        ];
    }

    /**
     * @dataProvider provideBuildRules
     */
    public function testBuildRules($data, $expected)
    {
        $article = new \Cake\ORM\Entity($data);
        $this->Articles->save($article);
        $result = $article->errors();
        $this->assertEquals($expected, $result);
    }

    public function testAssociationsCreated()
    {
        $result = $this->Articles->associations()->keys();
        $expected = ['priorities'];
        $this->assertEquals($expected, $result);

        foreach ($result as $assoc) {
            $this->assertInstanceOf('\Cake\ORM\Association\BelongsTo', $this->Articles->association($assoc));
        }

        $result = $this->Articles->get(1);
    }
}

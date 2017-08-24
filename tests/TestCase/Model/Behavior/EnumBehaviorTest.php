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

namespace CakeDC\Enum\Test\TestCase\Model\Behavior;

use CakeDC\Enum\Model\Behavior\Strategy\AbstractStrategy;
use Cake\Core\Configure;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class ArticlesTable extends Table
{
    const STATUS_PUBLIC = 'Published';
    const STATUS_DRAFT = 'Drafted';
    const STATUS_ARCHIVE = 'Archived';

    const NODE_TYPE_PAGE = 'Page';
    const NODE_TYPE_BLOG = 'Blog';

    const NODE_GROUP_ACTIVE = 'Active';

    const NORULES_FOO = 'Foo';

    public function initialize(array $config)
    {
        $this->addBehavior('CakeDC/Enum.Enum', ['lists' => [
            'priority' => ['errorMessage' => 'Invalid priority', 'prefix' => 'PRIORITY'],
            'status' => ['strategy' => 'const'],
            'category' => ['strategy' => 'config'],
            'node_type' => ['strategy' => 'const'],
            'node_group' => ['strategy' => 'const', 'lowercase' => true],
            'norules' => ['strategy' => 'const', 'applicationRules' => false],
        ]]);
    }
}

class ThirdPartyStrategy extends AbstractStrategy
{
    public function enum(array $config = [])
    {
        return [
            1 => 'PHP',
            2 => 'CSS'
        ];
    }
}

class EnumBehaviorTest extends TestCase
{
    public $fixtures = [
        'plugin.CakeDC/Enum.articles',
        'plugin.CakeDC/Enum.lookups',
    ];

    protected $Articles;

    public function setUp()
    {
        parent::setUp();

        Configure::write('CakeDC/Enum', [
            'ARTICLE_CATEGORY' => [
                'CakePHP',
                'Open Source Software',
            ]
        ]);

        $this->Articles = TableRegistry::get('CakeDC/Enum.Articles', [
            'className' => 'CakeDC\Enum\Test\TestCase\Model\Behavior\ArticlesTable',
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
            'translate' => false,
            'translationDomain' => 'default',
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
                    'errorMessage' => 'The provided value is invalid',
                    'lowercase' => false,
                ],
                'category' => [
                    'strategy' => 'config',
                    'prefix' => 'ARTICLE_CATEGORY',
                    'field' => 'category',
                    'errorMessage' => 'The provided value is invalid'
                ],
                'node_type' => [
                    'strategy' => 'const',
                    'prefix' => 'NODE_TYPE',
                    'field' => 'node_type',
                    'errorMessage' => 'The provided value is invalid',
                    'lowercase' => false
                ],
                'node_group' => [
                    'strategy' => 'const',
                    'prefix' => 'NODE_GROUP',
                    'field' => 'node_group',
                    'errorMessage' => 'The provided value is invalid',
                    'lowercase' => true
                ],
            ],
            'classMap' => []
        ];

        return [
            [
                [
                    'lists' => [
                        'priority' => ['errorMessage' => 'Invalid priority', 'prefix' => 'PRIORITY'],
                        'status' => ['strategy' => 'const', 'prefix' => 'STATUS'],
                        'category' => ['strategy' => 'config'],
                        'node_type' => ['strategy' => 'const'],
                        'node_group' => ['strategy' => 'const', 'lowercase' => true],
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
        $Articles = TableRegistry::get('CakeDC/Enum.Articles', ['table' => 'enum_articles']);
        $Articles->addBehavior('CakeDC/Enum.Enum', $config);
        $result = $Articles->behaviors()->Enum->config();
        $this->assertEquals($expected, $result);
    }

    public function provideBasicLookups()
    {
        return [
            [
                'priority',
                [
                    'URGENT' => 'Urgent',
                    'HIGH' => 'High',
                    'NORMAL' => 'Normal',
                ]
            ],
            [
                'status',
                [
                    'PUBLIC' => 'Published',
                    'DRAFT' => 'Drafted',
                    'ARCHIVE' => 'Archived',
                ]
            ],
            [
                'category',
                [
                    'CakePHP',
                    'Open Source Software',
                ]
            ],
            [
                'node_type',
                [
                    'PAGE' => 'Page',
                    'BLOG' => 'Blog',
                ]
            ],
            [
                'node_group',
                [
                    'active' => 'Active',
                ]
            ],
            [
                'norules',
                [
                    'FOO' => 'Foo',
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
                    'priority' => 'URGENT',
                    'status' => 'DRAFT',
                    'category' => 2,
                    'node_type' => 'BLOG',
                    'node_group' => 'active',
                    'norules' => 'invalid',
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
                    'node_type' => 'Invalid value',
                    'node_group' => 'active',
                    'norules' => 'invalid'
                ],
                [
                    'priority' => ['isValidPriority' => 'Invalid priority'],
                    'status' => ['isValidStatus' => 'The provided value is invalid'],
                    'node_type' => ['isValidNodeType' => 'The provided value is invalid'],
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

    public function testEnumNested()
    {
        $this->Articles->behaviors()->Enum->config('nested', true);
        $result = $this->Articles->enum('priority');
        $expected = [
            ['value' => 'URGENT', 'text' => 'Urgent'],
            ['value' => 'HIGH', 'text' => 'High'],
            ['value' => 'NORMAL', 'text' => 'Normal'],
        ];
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

    public function testEnumMultipleAlias()
    {
        $result = $this->Articles->enum();
        $expected = [
            'priority' => [
                'URGENT' => 'Urgent',
                'HIGH' => 'High',
                'NORMAL' => 'Normal',
            ],
            'status' => [
                'PUBLIC' => 'Published',
                'DRAFT' => 'Drafted',
                'ARCHIVE' => 'Archived',
            ],
            'category' => [
                'CakePHP',
                'Open Source Software',
            ],
            'node_type' => [
                'PAGE' => 'Page',
                'BLOG' => 'Blog',
            ],
            'node_group' => [
                'active' => 'Active',
            ],
            'norules' => [
                'FOO' => 'Foo',
            ],
        ];
        $this->assertEquals($expected, $result);

        $result = $this->Articles->enum([]);
        $this->assertEquals($expected, $result);

        $result = $this->Articles->enum(['priority', 'status']);
        $expected = [
            'priority' => [
                'URGENT' => 'Urgent',
                'HIGH' => 'High',
                'NORMAL' => 'Normal',
            ],
            'status' => [
                'PUBLIC' => 'Published',
                'DRAFT' => 'Drafted',
                'ARCHIVE' => 'Archived',
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    public function testTranslatedValues()
    {
        $this->Articles->behaviors()->Enum->config('translate', true);
        $result = $this->Articles->enum('node_group');

        $this->assertEquals(['active' => 'Translated Active'], $result);

        $result = $this->Articles->enum(['node_group', 'norules']);

        $expected = [
            'node_group' => ['active' => 'Translated Active'],
            'norules' => ['FOO' => 'translated foo']
        ];
        $this->assertEquals($expected, $result);
    }

    public function provideThirdPartyStrategy()
    {
        return [
            [
                [
                    'classMap' => ['third_party' => 'CakeDC\Enum\Test\TestCase\Model\Behavior\ThirdPartyStrategy'],
                    'lists' => [
                        'article_category' => ['strategy' => 'third_party']
                    ]
                ],
                [
                    1 => 'PHP',
                    2 => 'CSS'
                ]
            ]
        ];
    }

    /**
     * @dataProvider provideThirdPartyStrategy
     */
    public function testThirdPartyStrategy(array $config, array $expected)
    {
        TableRegistry::clear();
        $Articles = TableRegistry::get('CakeDC/Enum.Articles', ['table' => 'enum_articles']);
        $Articles->addBehavior('CakeDC/Enum.Enum', $config);
        $result = $Articles->enum('article_category');
        $this->assertEquals($expected, $result);
    }
}

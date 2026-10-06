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

namespace CakeDC\Enum\Test\TestCase\Model\Behavior;

use Cake\Core\Configure;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\TestSuite\TestCase;
use Cake\Validation\Validator;
use CakeDC\Enum\Model\Behavior\Strategy\AbstractStrategy;

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses,Squiz.Classes.ClassFileName.NoMatch
class ArticlesTable extends Table
{
    public const STATUS_PUBLIC = 'Published';
    public const STATUS_DRAFT = 'Drafted';
    public const STATUS_ARCHIVE = 'Archived';

    public const NO_CHECK_PUBLIC = 'Published';
    public const NO_CHECK_DRAFT = 'Drafted';
    public const NO_CHECK_ARCHIVE = 'Archived';

    public const NODE_TYPE_PAGE = 'Page';
    public const NODE_TYPE_BLOG = 'Blog';

    public const NODE_GROUP_ACTIVE = 'Active';

    public const NORULES_FOO = 'Foo';

    public const OPTIONAL_BAR = 'Bar';

    public function initialize(array $config): void
    {
        $this->addBehavior('CakeDC/Enum.Enum', [
            'lists' => [
                'no_check' => ['strategy' => 'const', 'callBeforeFind' => false],
                'priority' => ['errorMessage' => 'Invalid priority', 'prefix' => 'PRIORITY'],
                'status' => ['strategy' => 'const'],
                'category' => ['strategy' => 'config'],
                'node_type' => ['strategy' => 'const'],
                'node_group' => ['strategy' => 'const', 'lowercase' => true],
                'norules' => ['strategy' => 'const', 'applicationRules' => false],
                'optional' => ['strategy' => 'const', 'lowercase' => true, 'allowEmpty' => true],
            ],
        ]);
    }
}

class ValidatedArticlesTable extends ArticlesTable
{
    public function initialize(array $config): void
    {
        $this->addBehavior('CakeDC/Enum.Enum', $config['enum']);
    }

    public function validationCustom(Validator $validator): Validator
    {
        return $validator;
    }
}

class ThirdPartyStrategy extends AbstractStrategy
{
    public function enum(array $config = []): array
    {
        return [
            1 => 'PHP',
            2 => 'CSS',
        ];
    }
}

class EnumBehaviorTest extends TestCase
// phpcs:enable PSR1.Classes.ClassDeclaration.MultipleClasses,Squiz.Classes.ClassFileName.NoMatch
{
    public array $fixtures = [
        'plugin.CakeDC/Enum.Articles',
        'plugin.CakeDC/Enum.Lookups',
    ];

    protected Table $Articles;

    public function setUp(): void
    {
        parent::setUp();

        Configure::write('CakeDC/Enum.ARTICLE_CATEGORY', [
                'CakePHP',
                'Open Source Software',
            ]);

        $this->Articles = $this->getTableLocator()->get('CakeDC/Enum.Articles', [
            'className' => ArticlesTable::class,
            'table' => 'enum_articles',
        ]);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->getTableLocator()->clear();
    }

    public static function provideBasicConfiguration(): array
    {
        $expected = [
            'defaultStrategy' => 'lookup',
            'translate' => false,
            'translationDomain' => 'default',
            'validation' => false,
            'implementedMethods' => ['enum' => 'enum'],
            'lists' => [
                'priority' => [
                    'strategy' => 'lookup',
                    'prefix' => 'PRIORITY',
                    'field' => 'priority',
                    'errorMessage' => 'Invalid priority',
                    'callBeforeFind' => true,
                ],
                'status' => [
                    'strategy' => 'const',
                    'prefix' => 'STATUS',
                    'field' => 'status',
                    'errorMessage' => 'The provided value is invalid',
                    'lowercase' => false,
                    'callBeforeFind' => true,
                ],
                'no_check' => [
                    'strategy' => 'const',
                    'prefix' => 'STATUS',
                    'field' => 'no_check',
                    'errorMessage' => 'The provided value is invalid',
                    'lowercase' => false,
                    'callBeforeFind' => false,
                ],
                'category' => [
                    'strategy' => 'config',
                    'prefix' => 'ARTICLE_CATEGORY',
                    'field' => 'category',
                    'errorMessage' => 'The provided value is invalid',
                    'callBeforeFind' => true,
                ],
                'node_type' => [
                    'strategy' => 'const',
                    'prefix' => 'NODE_TYPE',
                    'field' => 'node_type',
                    'errorMessage' => 'The provided value is invalid',
                    'lowercase' => false,
                    'callBeforeFind' => true,
                ],
                'node_group' => [
                    'strategy' => 'const',
                    'prefix' => 'NODE_GROUP',
                    'field' => 'node_group',
                    'errorMessage' => 'The provided value is invalid',
                    'lowercase' => true,
                    'callBeforeFind' => true,
                ],
                'optional' => [
                    'strategy' => 'const',
                    'prefix' => 'OPTIONAL',
                    'field' => 'optional',
                    'errorMessage' => 'The provided value is invalid',
                    'lowercase' => true,
                    'callBeforeFind' => true,
                ],
            ],
            'classMap' => [],
            'className' => 'CakeDC/Enum.Enum',
        ];

        return [
            [
                [
                    'lists' => [
                        'priority' => ['errorMessage' => 'Invalid priority', 'prefix' => 'PRIORITY'],
                        'status' => ['strategy' => 'const', 'prefix' => 'STATUS'],
                        'no_check' => ['strategy' => 'const', 'prefix' => 'STATUS', 'callBeforeFind' => false],
                        'category' => ['strategy' => 'config'],
                        'node_type' => ['strategy' => 'const'],
                        'node_group' => ['strategy' => 'const', 'lowercase' => true],
                        'optional' => ['strategy' => 'const', 'lowercase' => true],
                    ],
                ],
                $expected,
            ],
        ];
    }

    /**
     * @dataProvider provideBasicConfiguration
     */
    public function testBasicConfiguration(array $config, array $expected)
    {
        $this->getTableLocator()->clear();
        $Articles = $this->getTableLocator()->get('CakeDC/Enum.Articles', ['table' => 'enum_articles']);
        $Articles->addBehavior('CakeDC/Enum.Enum', $config);
        $result = $Articles->behaviors()->Enum->getConfig();
        $this->assertEquals($expected, $result);
    }

    public static function provideBasicLookups(): array
    {
        return [
            [
                'priority',
                [
                    'URGENT' => 'Urgent',
                    'HIGH' => 'High',
                    'NORMAL' => 'Normal',
                ],
            ],
            [
                'status',
                [
                    'PUBLIC' => 'Published',
                    'DRAFT' => 'Drafted',
                    'ARCHIVE' => 'Archived',
                ],
            ],
            [
                'category',
                [
                    'CakePHP',
                    'Open Source Software',
                ],
            ],
            [
                'node_type',
                [
                    'PAGE' => 'Page',
                    'BLOG' => 'Blog',
                ],
            ],
            [
                'node_group',
                [
                    'active' => 'Active',
                ],
            ],
            [
                'norules',
                [
                    'FOO' => 'Foo',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideBasicLookups
     */
    public function testBasicLookups($group, $expected)
    {
        $result = $this->Articles->getBehavior('Enum')->enum($group);
        $this->assertEquals($expected, $result);
    }

    public static function provideBuildRules(): array
    {
        return [
            [
                [
                    'priority' => 'URGENT',
                    'status' => 'DRAFT',
                    'no_check' => 'DRAFT',
                    'category' => 2,
                    'node_type' => 'BLOG',
                    'node_group' => 'active',
                    'norules' => 'invalid',
                    'optional' => 'bar',
                ],
                [
                    'category' => ['isValidCategory' => 'The provided value is invalid'],
                ],
            ],
            [
                [
                    'priority' => 'Urgent',
                    'status' => 'Drafted',
                    'no_check' => 'Drafted',
                    'category' => 1,
                    'node_type' => 'Invalid value',
                    'node_group' => 'active',
                    'norules' => 'invalid',
                ],
                [
                    'priority' => ['isValidPriority' => 'Invalid priority'],
                    'status' => ['isValidStatus' => 'The provided value is invalid'],
                    'node_type' => ['isValidNodeType' => 'The provided value is invalid'],
                    'no_check' => ['isValidNoCheck' => 'The provided value is invalid'],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideBuildRules
     */
    public function testBuildRules($data, $expected)
    {
        $article = new Entity($data);
        $this->Articles->save($article);
        $result = $article->getErrors();
        $this->assertEquals($expected, $result);
    }

    public function testEnumNested()
    {
        $this->Articles->behaviors()->Enum->setConfig('nested', true);
        $result = $this->Articles->getBehavior('Enum')->enum('priority');
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
        $expected = ['Priorities'];
        $this->assertEquals($expected, $result);

        foreach ($result as $assoc) {
            $this->assertInstanceOf(BelongsTo::class, $this->Articles->getAssociation($assoc));
        }

        $this->Articles->get(1);
    }

    public function testEnumMultipleAlias()
    {
        $result = $this->Articles->getBehavior('Enum')->enum();
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
            'no_check' => [
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
            'optional' => [
                'bar' => 'Bar',
            ],
        ];
        $this->assertEquals($expected, $result);

        $result = $this->Articles->getBehavior('Enum')->enum([]);
        $this->assertEquals($expected, $result);

        $result = $this->Articles->getBehavior('Enum')->enum(['priority', 'status']);
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
        $this->Articles->behaviors()->Enum->setConfig('translate', true);
        $result = $this->Articles->getBehavior('Enum')->enum('node_group');

        $this->assertEquals(['active' => 'Translated Active'], $result);

        $result = $this->Articles->getBehavior('Enum')->enum(['node_group', 'norules']);

        $expected = [
            'node_group' => ['active' => 'Translated Active'],
            'norules' => ['FOO' => 'translated foo'],
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * Builds an articles table with the Enum behavior using the given configuration.
     *
     * @param array<string, mixed> $config Behavior configuration.
     * @return \Cake\ORM\Table
     */
    protected function articlesWithEnum(array $config): Table
    {
        $this->getTableLocator()->clear();

        return $this->getTableLocator()->get('CakeDC/Enum.Articles', [
            'className' => ValidatedArticlesTable::class,
            'table' => 'enum_articles',
            'enum' => $config,
        ]);
    }

    public function testValidationRejectsInvalidValue()
    {
        $Articles = $this->articlesWithEnum([
            'lists' => [
                'status' => ['strategy' => 'const', 'validation' => true],
            ],
        ]);

        $article = $Articles->newEntity(['status' => 'bogus']);
        $expected = ['status' => ['isValidStatus' => 'The provided value is invalid']];
        $this->assertEquals($expected, $article->getErrors());

        $article = $Articles->newEntity(['status' => 'DRAFT']);
        $this->assertEmpty($article->getErrors());
    }

    public function testValidationDisabledByDefault()
    {
        $Articles = $this->articlesWithEnum([
            'lists' => [
                'status' => ['strategy' => 'const'],
            ],
        ]);

        $article = $Articles->newEntity(['status' => 'bogus']);
        $this->assertEmpty($article->getErrors());
    }

    public function testValidationOnNamedValidatorOnly()
    {
        $Articles = $this->articlesWithEnum([
            'lists' => [
                'status' => ['strategy' => 'const', 'validation' => ['custom']],
            ],
        ]);

        $article = $Articles->newEntity(['status' => 'bogus']);
        $this->assertEmpty($article->getErrors());

        $article = $Articles->newEntity(['status' => 'bogus'], ['validate' => 'custom']);
        $expected = ['status' => ['isValidStatus' => 'The provided value is invalid']];
        $this->assertEquals($expected, $article->getErrors());
    }

    public function testValidationFromBehaviorDefault()
    {
        $Articles = $this->articlesWithEnum([
            'validation' => true,
            'lists' => [
                'status' => ['strategy' => 'const'],
                'node_type' => ['strategy' => 'const', 'validation' => false],
            ],
        ]);

        $article = $Articles->newEntity(['status' => 'bogus', 'node_type' => 'bogus']);
        $expected = ['status' => ['isValidStatus' => 'The provided value is invalid']];
        $this->assertEquals($expected, $article->getErrors());
    }

    public function testValidationAllowEmpty()
    {
        $Articles = $this->articlesWithEnum([
            'lists' => [
                'optional' => [
                    'strategy' => 'const',
                    'lowercase' => true,
                    'allowEmpty' => true,
                    'validation' => true,
                ],
            ],
        ]);

        $article = $Articles->newEntity(['optional' => '']);
        $this->assertEmpty($article->getErrors());

        $article = $Articles->newEntity(['optional' => 'bogus']);
        $expected = ['optional' => ['isValidOptional' => 'The provided value is invalid']];
        $this->assertEquals($expected, $article->getErrors());
    }

    public function testValidationLookupStrategy()
    {
        $Articles = $this->articlesWithEnum([
            'lists' => [
                'priority' => ['prefix' => 'PRIORITY', 'errorMessage' => 'Invalid priority', 'validation' => true],
            ],
        ]);

        $article = $Articles->newEntity(['priority' => 'Urgent']);
        $expected = ['priority' => ['isValidPriority' => 'Invalid priority']];
        $this->assertEquals($expected, $article->getErrors());

        $article = $Articles->newEntity(['priority' => 'HIGH']);
        $this->assertEmpty($article->getErrors());
    }

    public function testValidationIgnoresNestedAndTranslateOptions()
    {
        $Articles = $this->articlesWithEnum([
            'nested' => true,
            'translate' => true,
            'lists' => [
                'node_group' => ['strategy' => 'const', 'lowercase' => true, 'validation' => true],
            ],
        ]);

        $article = $Articles->newEntity(['node_group' => 'active']);
        $this->assertEmpty($article->getErrors());
    }

    public function testBuildRulesWithNestedEnum()
    {
        $this->Articles->behaviors()->Enum->setConfig('nested', true);

        $article = new Entity(['status' => 'DRAFT', 'norules' => 'invalid']);
        $this->Articles->save($article);
        $this->assertArrayNotHasKey('status', $article->getErrors());
    }

    public static function provideThirdPartyStrategy(): array
    {
        return [
            [
                [
                    'classMap' => ['third_party' => ThirdPartyStrategy::class],
                    'lists' => [
                        'article_category' => ['strategy' => 'third_party'],
                    ],
                ],
                [
                    1 => 'PHP',
                    2 => 'CSS',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideThirdPartyStrategy
     */
    public function testThirdPartyStrategy(array $config, array $expected)
    {
        $this->getTableLocator()->clear();
        $Articles = $this->getTableLocator()->get('CakeDC/Enum.Articles', ['table' => 'enum_articles']);
        $Articles->addBehavior('CakeDC/Enum.Enum', $config);
        $result = $Articles->getBehavior('Enum')->enum('article_category');
        $this->assertEquals($expected, $result);
    }
}

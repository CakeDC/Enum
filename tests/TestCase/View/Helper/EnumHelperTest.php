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

namespace CakeDC\Enum\Test\TestCase\View\Helper;

use CakeDC\Enum\View\Helper\EnumHelper;
use Cake\ORM\Table;
use Cake\TestSuite\TestCase;
use Cake\View\View;

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

class EnumHelperTest extends TestCase
{
    public $fixtures = [
        'plugin.CakeDC/Enum.lookups',
    ];

    public $helper = null;

    public function setUp()
    {
        parent::setUp();
        $View = new View();
        $this->helper = new EnumHelper($View);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->helper);
    }

    public function testEnumInput()
    {
        $this->helper->setClassName('CakeDC\Enum\Test\TestCase\View\Helper\ArticlesTable');
        $expected = '<div class="input select"><label for="articles-field">Field</label><select name="Articles[field]" id="articles-field"><option value="URGENT">Urgent</option><option value="HIGH">High</option><option value="NORMAL">Normal</option></select></div>';
        $result = $this->helper->input('Articles.field', '', ['alias' => 'priority']);
        $this->assertEquals($expected, $result);

        $expected = '<div class="input select"><label for="field">Field</label><select name="field" id="field"><option value="PUBLIC">Published</option><option value="DRAFT">Drafted</option><option value="ARCHIVE">Archived</option></select></div>';
        $result = $this->helper->input('field', 'Articles', ['alias' => 'status']);
        $this->assertEquals($expected, $result);

        $expected = '<div class="input select"><label for="articles-field">Field</label><select name="Articles[field]" id="articles-field"></select></div>';
        $result = $this->helper->input('Articles.field', '', ['alias' => 'category']);
        $this->assertEquals($expected, $result);

        $expected = '<div class="input select"><label for="field">Field</label><select name="field" id="field"><option value="PAGE">Page</option><option value="BLOG">Blog</option></select></div>';
        $result = $this->helper->input('field', 'Articles', ['alias' => 'node_type']);
        $this->assertEquals($expected, $result);

        $expected = '<div class="input select"><label for="articles-field">Field</label><select name="Articles[field]" id="articles-field"><option value="active">Active</option></select></div>';
        $result = $this->helper->input('Articles.field', '', ['alias' => 'node_group']);
        $this->assertEquals($expected, $result);

        $expected = '<div class="input select"><label for="field">Field</label><select name="field" id="field"><option value="FOO">Foo</option></select></div>';
        $result = $this->helper->input('field', 'Articles', ['alias' => 'norules']);
        $this->assertEquals($expected, $result);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testEnumInputException()
    {
        $this->helper->setClassName('CakeDC\Enum\Test\TestCase\View\Helper\ArticlesTable');
        $this->helper->input('field');
    }

    public function testEnumInputWithoutClassName()
    {
        $this->helper->input('field', 'Articles', ['alias' => 'priority']);
    }
}

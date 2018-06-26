<?php

/**
 * Copyright 2015 - 2018, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2015 - 2018, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

use Aura\Intl\Package;
use Cake\I18n\I18n;

$findRoot = function ($root) {
    do {
        $lastRoot = $root;
        $root = dirname($root);
        if (is_dir($root . '/vendor/cakephp/cakephp')) {
            return $root;
        }
    } while ($root !== $lastRoot);

    throw new Exception("Cannot find the root of the application, unable to run tests");
};
$root = $findRoot(__FILE__);
unset($findRoot);

chdir($root);
if (file_exists($root . '/config/bootstrap.php')) {
    require $root . '/config/bootstrap.php';
}

require $root . '/vendor/cakephp/cakephp/tests/bootstrap.php';

\Cake\Core\Plugin::load('CakeDC/Enum', ['path' => dirname(dirname(__FILE__)) . DS]);

I18n::config('default', function ($name, $locale) {
    $package = new Package('default');
    $messages = [
        'Active' => 'Translated Active',
        'Foo' => 'translated foo'
    ];
    $package->setMessages($messages);

    return $package;
});

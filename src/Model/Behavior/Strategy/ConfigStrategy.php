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

namespace CakeDC\Enum\Model\Behavior\Strategy;

use Cake\Core\Configure;

class ConfigStrategy extends AbstractStrategy
{
    public const KEY = 'CakeDC/Enum';

    /**
     * {@inheritdoc}
     *
     * @param array $config (unused in this case).
     * @return array
     */
    public function enum(array $config = []): array
    {
        return (array)Configure::read(self::KEY . '.' . $this->getConfig('prefix'));
    }
}

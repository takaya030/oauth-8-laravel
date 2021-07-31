<?php

namespace Takaya030\OAuth\Facade;

/**
 * @author     Dariusz Prz?da <artdarek@gmail.com>
 * @copyright  Copyright (c) 2013
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

use Illuminate\Support\Facades\Facade;

class OAuth extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Takaya030\OAuth\OAuth';
    }
}

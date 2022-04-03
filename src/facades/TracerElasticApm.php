<?php

namespace Yuchanns\ElasticApmTracer\facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class TracerElasticApm
 * @package Yuchanns\ElasticApmTracer\Facades
 * @method static getSpan($name, $type, $subType = null, $action = null)
 * @method static endSpan($name)
 */
class TracerElasticApm extends Facade
{
    public static function getFacadeAccessor()
    {
        return \Yuchanns\ElasticApmTracer\TracerElasticApm::class;
    }
}


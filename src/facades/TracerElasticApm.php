<?php

namespace Yuchanns\ElasticApmTracer\facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class TracerElasticApm
 * @package Yuchanns\ElasticApmTracer\Facades
 * @method static inject(array $header)
 */
class TracerElasticApm extends Facade
{
    public static function getFacadeAccessor()
    {
        return \Yuchanns\ElasticApmTracer\TracerElasticApm::class;
    }
}


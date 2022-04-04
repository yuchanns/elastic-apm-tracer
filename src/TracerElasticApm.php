<?php

namespace Yuchanns\ElasticApmTracer;

use Illuminate\Contracts\Foundation\Application;
use Elastic\Apm\ElasticApm;

class TracerElasticApm
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function inject(array $header)
    {
        ElasticApm::getCurrentTransaction()->injectDistributedTracingHeaders(function(string $name, string $value) use (&$header): void {
            if (!isset($header[$name])) {
                $header[$name] = $value;
            }
        });

        return $header;
    }
}
